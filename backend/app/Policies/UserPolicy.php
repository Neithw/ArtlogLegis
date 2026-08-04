<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('usuarios:visualizar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('usuarios:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $targetUser): bool
    {
        return $this->pertenceAoEscopo($user, $targetUser)
            && $user->hasPermission('usuarios:editar');
    }

    /**
     * Determine whether the user can DEACTIVATE the model.
     */
    public function desativar(User $user, User $targetUser): bool
    {
        return $this->pertenceAoEscopo($user, $targetUser)
            && $user->hasPermission('usuarios:desativar');
    }

    /**
     * Determine whether the user can REACTIVATE the model.
     */
    public function reativar(User $user, User $targetUser): bool
    {
        return $this->pertenceAoEscopo($user, $targetUser)
            && $user->hasPermission('usuarios:reativar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->isRoot();
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $usuarioAutenticado, User $usuarioAlvo): bool
    {
        return $this->possuiCamara($usuarioAutenticado)
            && (int) $usuarioAutenticado->camara_id === (int) $usuarioAlvo->camara_id;
    }
}
