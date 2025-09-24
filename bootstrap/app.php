<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Evitar redirecciones a la ruta 'login' en APIs
        $middleware->redirectGuestsTo(fn () => null);

        // Registrar middleware JWT personalizado
        $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JWTAuthMiddleware::class,
        ]);

        // Usar el middleware CORS de Laravel
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejar excepciones de autenticación para APIs
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado',
                    'error' => 'Token de acceso requerido'
                ], 401);
            }
        });
    })->create();
