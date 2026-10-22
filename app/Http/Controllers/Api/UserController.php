<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        return response()->json(User::create($datos), 201);
    }

    public function show(User $user)
    {
        return $user->load('tareas');
    }

    public function update(Request $request, User $user)
    {
        $datos = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        $user->update($datos);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
