<?php

namespace App\Policies;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Regla anti-BOLA: una tarea solo la puede ver, editar o borrar su dueño (o un admin).
 * Laravel la encuentra sola por el nombre (Tarea -> TareaPolicy).
 */
class TareaPolicy
{
    public function view(User $user, Tarea $tarea): Response
    {
        return $this->duenoOAdmin($user, $tarea);
    }

    public function update(User $user, Tarea $tarea): Response
    {
        return $this->duenoOAdmin($user, $tarea);
    }

    public function delete(User $user, Tarea $tarea): Response
    {
        return $this->duenoOAdmin($user, $tarea);
    }

    private function duenoOAdmin(User $user, Tarea $tarea): Response
    {
        // 404 y no 403: a quien no es dueño ni siquiera se le confirma que la tarea existe.
        return $user->esAdmin() || $tarea->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
