<?php

namespace App\Policies;

use App\Models\Camara;
use App\Models\User;

class CamaraPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->camara_id !== null
            && $user->hasPermission('camaras:visualizar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Camara $camara): bool
    {
        return $user->hasPermission('camaras:editar')
            && (int) $user->camara_id === (int) $camara->id;
    }

    public function desativar(User $user, Camara $camara): bool
    {
        return false;
    }

    public function reativar(User $user, Camara $camara): bool
    {
        return false;
    }
}
