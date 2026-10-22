<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create($datos);

        return $this->respuestaConToken(auth('api')->login($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // attempt() busca el usuario, compara la contraseña con el hash y, si coincide, firma un JWT.
        if (! $token = auth('api')->attempt($credenciales)) {
            return response()->json(['message' => 'Email o contraseña incorrectos.'], 401);
        }

        return $this->respuestaConToken($token);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    public function logout(): JsonResponse
    {
        // Mete el token en la lista negra: aunque no haya caducado, ya no se acepta.
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function refresh(): JsonResponse
    {
        // Invalida el token actual y entrega uno nuevo con la caducidad reiniciada.
        return $this->respuestaConToken(auth('api')->refresh());
    }

    private function respuestaConToken(string $token, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->setToken($token)->user(),
        ], $status);
    }
}
