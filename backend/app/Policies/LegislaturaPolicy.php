<?php

namespace App\Policies;

use App\Models\Legislatura;
use App\Models\User;

class LegislaturaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('legislaturas:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Legislatura $legislatura): bool
    {
        return $this->pertenceAoEscopo($user, $legislatura)
            && $user->hasPermission('legislaturas:visualizar');
    }

    public function viewArchived(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('legislaturas:restaurar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('legislaturas:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Legislatura $legislatura): bool
    {
        return $this->pertenceAoEscopo($user, $legislatura)
            && $user->hasPermission('legislaturas:editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Legislatura $legislatura): bool
    {
        return $this->pertenceAoEscopo($user, $legislatura)
            && $user->hasPermission('legislaturas:excluir');
    }

    public function restore(User $user, Legislatura $legislatura): bool
    {
        return $this->pertenceAoEscopo($user, $legislatura)
            && $user->hasPermission('legislaturas:restaurar');
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, Legislatura $legislatura): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $legislatura->camara_id;
    }
}
