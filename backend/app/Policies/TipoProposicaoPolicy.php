<?php

namespace App\Policies;

use App\Models\TipoProposicao;
use App\Models\User;

class TipoProposicaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('tipos-proposicao:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TipoProposicao $tipoProposicao): bool
    {
        return $this->pertenceAoEscopo($user, $tipoProposicao)
            && $user->hasPermission('tipos-proposicao:visualizar');
    }

    public function viewArchived(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('tipos-proposicao:restaurar');
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('tipos-proposicao:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoProposicao $tipoProposicao): bool
    {
        return $this->pertenceAoEscopo($user, $tipoProposicao)
            && $user->hasPermission('tipos-proposicao:editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoProposicao $tipoProposicao): bool
    {
        return $this->pertenceAoEscopo($user, $tipoProposicao)
            && $user->hasPermission('tipos-proposicao:excluir');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoProposicao $tipoProposicao): bool
    {
        return $this->pertenceAoEscopo($user, $tipoProposicao)
            && $user->hasPermission('tipos-proposicao:restaurar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoProposicao $tipoProposicao): bool
    {
        return false;
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, TipoProposicao $tipoProposicao): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $tipoProposicao->camara_id;
    }
}
