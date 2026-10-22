<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TareaController extends Controller
{
    public function index(Request $request)
    {
        // El admin ve todas; un usuario normal, solo las suyas.
        return $request->user()->esAdmin()
            ? Tarea::all()
            : $request->user()->tareas;
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'completada' => 'boolean',
        ]);

        // El dueño sale del token. Solo un admin puede crear tareas para otro usuario.
        $dueno = $request->user()->esAdmin() && isset($datos['user_id'])
            ? User::find($datos['user_id'])
            : $request->user();

        return response()->json($dueno->tareas()->create($datos), 201);
    }

    public function show(Tarea $tarea)
    {
        Gate::authorize('view', $tarea);

        return $tarea;
    }

    public function update(Request $request, Tarea $tarea)
    {
        Gate::authorize('update', $tarea);

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
        Gate::authorize('delete', $tarea);

        $tarea->delete();

        return response()->noContent();
    }
}
