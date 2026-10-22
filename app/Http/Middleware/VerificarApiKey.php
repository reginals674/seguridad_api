<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plana = $request->header('X-API-KEY');

        if (! $plana) {
            return response()->json(['message' => 'Falta la API key (header X-API-KEY).'], 401);
        }

        $apiKey = ApiKey::buscarActiva($plana);

        if (! $apiKey) {
            return response()->json(['message' => 'API key inválida o revocada.'], 401);
        }

        $apiKey->forceFill(['ultimo_uso_at' => now()])->saveQuietly();

        return $next($request);
    }
}
