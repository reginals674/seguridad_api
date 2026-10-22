<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        return Tarea::all();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'completada' => 'boolean',
        ]);

        return response()->json(Tarea::create($datos), 201);
    }

    public function show(Tarea $tarea)
    {
        return $tarea;
    }

    public function update(Request $request, Tarea $tarea)
    {
        $datos = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'completada' => 'boolean',
        ]);

        $tarea->update($datos);

        return $tarea;
    }

    public function destroy(Tarea $tarea)
    {
        $tarea->delete();

        return response()->noContent();
    }
}
