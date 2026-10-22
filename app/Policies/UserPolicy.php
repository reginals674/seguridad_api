<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Cada usuario solo accede a su propio perfil. El admin puede con todos.
 */
class UserPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->esAdmin()
            ? Response::allow()
            : Response::deny('Solo un administrador puede listar usuarios.');
    }

    public function view(User $user, User $perfil): Response
    {
        return $this->mismoOAdmin($user, $perfil);
    }

    public function update(User $user, User $perfil): Response
    {
        return $this->mismoOAdmin($user, $perfil);
    }

    public function delete(User $user, User $perfil): Response
    {
        if ($user->esAdmin()) {
            return Response::allow();
        }

        return $user->is($perfil)
            ? Response::deny('Solo un administrador puede borrar usuarios.')
            : Response::denyAsNotFound();
    }

    private function mismoOAdmin(User $user, User $perfil): Response
    {
        return $user->esAdmin() || $user->is($perfil)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
