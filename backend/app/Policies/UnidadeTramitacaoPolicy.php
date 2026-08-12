<?php

namespace App\Policies;

use App\Models\UnidadeTramitacao;
use App\Models\User;

class UnidadeTramitacaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('unidades-tramitacao:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $unidadeTramitacao)
            && $user->hasPermission('unidades-tramitacao:visualizar');
    }

    public function viewArchived(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('unidades-tramitacao:restaurar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('unidades-tramitacao:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $unidadeTramitacao)
            && $user->hasPermission('unidades-tramitacao:editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $unidadeTramitacao)
            && $user->hasPermission('unidades-tramitacao:excluir');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $unidadeTramitacao)
            && $user->hasPermission('unidades-tramitacao:restaurar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return false;
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, UnidadeTramitacao $unidadeTramitacao): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $unidadeTramitacao->camara_id;
    }
}
