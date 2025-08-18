<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RobotsHeaders
{
    /**
     * Add X-Robots-Tag header for disallowed / low-value routes.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $noindexGlobs = [
            'admin*', 'dashboard', 'profile*', 'tickets*', 'inventory*',
            'checkout*', 'shipping*', 'orders/*/cancel', 'api/*', 'stripe/webhook'
        ];

        if (collect($noindexGlobs)->some(fn ($p) => Str::is($p, $request->path())) ) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        return $response;
    }
}
