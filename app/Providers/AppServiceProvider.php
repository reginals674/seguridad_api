<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paso 4 - Rate Limiter. Los contadores se guardan en la caché (tabla "cache" de MySQL).

        // Uso general: 60 peticiones por minuto. Se cuenta por usuario si el JWT es válido;
        // si no, por IP (así tampoco se puede martillar la API probando API keys o tokens).
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user('api');

            return Limit::perMinute(60)
                ->by($user ? 'user:'.$user->id : 'ip:'.$request->ip())
                ->response(fn (Request $request, array $headers) => $this->demasiadas($headers));
        });

        // Login y registro: mucho más estricto contra fuerza bruta.
        // - 5 intentos por minuto para un mismo email (protege la cuenta atacada).
        // - 20 por minuto desde una misma IP (frena a quien prueba muchos emails distintos).
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)
                    ->by('login:'.$email.'|'.$request->ip())
                    ->response(fn (Request $request, array $headers) => $this->demasiadas($headers)),
                Limit::perMinute(20)
                    ->by('login-ip:'.$request->ip())
                    ->response(fn (Request $request, array $headers) => $this->demasiadas($headers)),
            ];
        });
    }

    /**
     * Respuesta 429 en JSON; las cabeceras incluyen Retry-After (segundos a esperar).
     *
     * @param  array<string, mixed>  $headers
     */
    private function demasiadas(array $headers)
    {
        return response()->json([
            'message' => 'Demasiadas peticiones. Intenta de nuevo en '.($headers['Retry-After'] ?? 60).' segundos.',
        ], 429, $headers);
    }
}
