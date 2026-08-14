<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vereador;

class VereadorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('vereadores:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vereador $vereador): bool
    {
        return $this->pertenceAoEscopo($user, $vereador)
            && $user->hasPermission('vereadores:visualizar');
    }

    public function viewArchived(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('vereadores:restaurar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('vereadores:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vereador $vereador): bool
    {
        return $this->pertenceAoEscopo($user, $vereador)
            && $user->hasPermission('vereadores:editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vereador $vereador): bool
    {
        return $this->pertenceAoEscopo($user, $vereador)
            && $user->hasPermission('vereadores:excluir');
    }

    public function restore(User $user, Vereador $vereador): bool
    {
        return $this->pertenceAoEscopo($user, $vereador)
            && $user->hasPermission('vereadores:restaurar');
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, Vereador $vereador): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $vereador->camara_id;
    }
}
