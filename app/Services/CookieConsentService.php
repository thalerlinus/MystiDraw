<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CookieConsentService
{
    /**
     * Check if user has consented to specific cookie category
     */
    public static function hasConsent(Request $request, string $category): bool
    {
        $consent = self::getCurrentConsent($request);
        return isset($consent[$category]) && $consent[$category] === true;
    }

    /**
     * Check if user has consented to analytics cookies
     */
    public static function hasAnalyticsConsent(Request $request): bool
    {
        return self::hasConsent($request, 'analytics');
    }

    /**
     * Check if user has consented to marketing cookies
     */
    public static function hasMarketingConsent(Request $request): bool
    {
        return self::hasConsent($request, 'marketing');
    }

    /**
     * Check if user has consented to functional cookies
     */
    public static function hasFunctionalConsent(Request $request): bool
    {
        return self::hasConsent($request, 'functional');
    }

    /**
     * Get all consent settings
     */
    public static function getConsent(Request $request): array
    {
        return self::getCurrentConsent($request) ?: [
            'necessary' => true,
            'analytics' => false,
            'marketing' => false,
            'functional' => false
        ];
    }

    /**
     * Check if user has given any consent
     */
    public static function hasAnyConsent(Request $request): bool
    {
        return !empty(self::getCurrentConsent($request));
    }

    /**
     * Get current consent from cookie or database
     */
    private static function getCurrentConsent(Request $request): ?array
    {
        // First try to get from cookie
        $cookieConsent = $request->cookie('mystidraw_cookie_consent');
        
        if ($cookieConsent) {
            try {
                $data = json_decode($cookieConsent, true);
                if (isset($data['consent']) && is_array($data['consent'])) {
                    return $data['consent'];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to decode cookie consent', [
                    'cookie' => $cookieConsent,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // If user is authenticated, try to get from database
        if ($request->user()) {
            return self::getConsentFromDatabase($request->user());
        }

        return null;
    }

    /**
     * Get consent from database for authenticated users
     */
    private static function getConsentFromDatabase($user): ?array
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

    /**
     * Generate Google Analytics consent mode configuration
     */
    public static function getGoogleConsentMode(Request $request): array
    {
        $consent = self::getConsent($request);
        
        return [
            'ad_storage' => $consent['marketing'] ? 'granted' : 'denied',
            'ad_user_data' => $consent['marketing'] ? 'granted' : 'denied',
            'ad_personalization' => $consent['marketing'] ? 'granted' : 'denied',
            'analytics_storage' => $consent['analytics'] ? 'granted' : 'denied',
            'functionality_storage' => $consent['functional'] ? 'granted' : 'denied',
            'personalization_storage' => $consent['functional'] ? 'granted' : 'denied',
            'security_storage' => 'granted' // Always granted for necessary cookies
        ];
    }

    /**
     * Check if Google Analytics should be loaded
     */
    public static function shouldLoadGoogleAnalytics(Request $request): bool
    {
        return self::hasAnalyticsConsent($request);
    }

    /**
     * Check if Google Ads/Marketing pixels should be loaded
     */
    public static function shouldLoadMarketingPixels(Request $request): bool
    {
        return self::hasMarketingConsent($request);
    }
}
