<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return User::all();
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return $user->load('tareas');
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $datos = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6',
            // Mandar "role" se rechaza (422) en vez de ignorarlo en silencio: el rol solo se cambia con usuario:rol.
            'role' => 'prohibited',
        ]);

        $user->update($datos);

        return $user;
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
