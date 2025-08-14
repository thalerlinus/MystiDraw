<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CookieConsentController extends Controller
{
    /**
     * Get current cookie consent status
     */
    public function getConsent(Request $request): JsonResponse
    {
        $consent = $this->getCurrentConsent($request);
        
        return response()->json([
            'hasConsent' => !empty($consent),
            'consent' => $consent ?: [
                'necessary' => true,
                'analytics' => false,
                'marketing' => false,
                'functional' => false
            ],
            'timestamp' => $consent ? now()->toISOString() : null
        ]);
    }

    /**
     * Save cookie consent preferences
     */
    public function saveConsent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'consent' => 'required|array',
            'consent.necessary' => 'required|boolean',
            'consent.analytics' => 'required|boolean',
            'consent.marketing' => 'required|boolean',
            'consent.functional' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid consent data',
                'errors' => $validator->errors()
            ], 422);
        }

        $consent = $request->input('consent');
        
        // Ensure necessary cookies are always true
        $consent['necessary'] = true;
        
        // Add metadata
        $consentData = [
            'consent' => $consent,
            'timestamp' => now()->toISOString(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'version' => '1.0'
        ];

        // Save to cookie (expires in 1 year)
        $cookie = Cookie::make(
            'mystidraw_cookie_consent',
            json_encode($consentData),
            60 * 24 * 365, // 1 year
            '/', // path
            null, // domain
            true, // secure (HTTPS only)
            true, // httpOnly
            false, // raw
            'strict' // sameSite
        );

        // If user is authenticated, also save to database
        if ($request->user()) {
            $this->saveConsentToDatabase($request->user(), $consentData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cookie consent saved successfully',
            'consent' => $consent
        ])->withCookie($cookie);
    }

    /**
     * Check if specific cookie category is consented
     */
    public function hasConsent(Request $request, string $category): JsonResponse
    {
        $consent = $this->getCurrentConsent($request);
        $hasConsent = isset($consent[$category]) && $consent[$category];

        return response()->json([
            'category' => $category,
            'hasConsent' => $hasConsent,
            'consent' => $consent
        ]);
    }

    /**
     * Reset cookie consent (for testing purposes)
     */
    public function resetConsent(Request $request): JsonResponse
    {
        $cookie = Cookie::forget('mystidraw_cookie_consent');

        return response()->json([
            'success' => true,
            'message' => 'Cookie consent reset successfully'
        ])->withCookie($cookie);
    }

    /**
     * Get current consent from cookie or database
     */
    private function getCurrentConsent(Request $request): ?array
    {
        // First try to get from cookie
        $cookieConsent = $request->cookie('mystidraw_cookie_consent');
        
        if ($cookieConsent) {
            $data = json_decode($cookieConsent, true);
            if (isset($data['consent']) && is_array($data['consent'])) {
                return $data['consent'];
            }
        }

        // If user is authenticated, try to get from database
        if ($request->user()) {
            return $this->getConsentFromDatabase($request->user());
        }

        return null;
    }

    /**
     * Save consent to database for authenticated users
     */
    private function saveConsentToDatabase($user, array $consentData): void
    {
        try {
            // We could create a cookie_consents table if needed
            // For now, we'll store it as JSON in user preferences or settings
            $user->update([
                'cookie_consent' => $consentData,
                'cookie_consent_updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to save cookie consent to database', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get consent from database for authenticated users
     */
    private function getConsentFromDatabase($user): ?array
    {
        try {
            if (isset($user->cookie_consent) && is_array($user->cookie_consent)) {
                return $user->cookie_consent['consent'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get cookie consent from database', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}
