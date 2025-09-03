<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\RobotsHeaders::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // CSRF-Token für Stripe Webhooks ausschließen
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Seite nicht gefunden'], 404);
            }
            
            return \Inertia\Inertia::render('Errors/404')->toResponse($request)->setStatusCode(404);
        });

        // Nur 500-Fehler auf die benutzerdefinierte 500-Seite mappen, sonst Standardverhalten beibehalten
        $exceptions->render(function (\Throwable $e, $request) {
            // Wenn es eine HttpException mit Status 500 ist → eigene 500-Seite
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && $e->getStatusCode() === 500) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Interner Serverfehler'], 500);
                }
                return \Inertia\Inertia::render('Errors/500')->toResponse($request)->setStatusCode(500);
            }

            // Nicht-HttpExceptions führen in Produktion (debug=false) zu 500 → eigene 500-Seite
            if (!config('app.debug') && !($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Interner Serverfehler'], 500);
                }
                return \Inertia\Inertia::render('Errors/500')->toResponse($request)->setStatusCode(500);
            }

            // Für alle anderen Fälle Standard-Handling (Status/Seite) beibehalten
            return null;
        });
    })
    ->withProviders([
        \App\Providers\BunnyCdnServiceProvider::class,
    ])
    ->create();
