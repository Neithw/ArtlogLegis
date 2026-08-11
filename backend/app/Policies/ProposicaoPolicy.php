<?php

namespace App\Policies;

use App\Models\Proposicao;
use App\Models\User;

class ProposicaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('proposicoes:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposicao $proposicao): bool
    {
        return $this->pertenceAoEscopo($user, $proposicao)
            && $user->hasPermission('proposicoes:visualizar');
    }

    public function viewArchived(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('proposicoes:restaurar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('proposicoes:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposicao $proposicao): bool
    {
        return $this->pertenceAoEscopo($user, $proposicao)
            && $user->hasPermission('proposicoes:editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposicao $proposicao): bool
    {
        return $this->pertenceAoEscopo($user, $proposicao)
            && $user->hasPermission('proposicoes:excluir');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proposicao $proposicao): bool
    {
        return $this->pertenceAoEscopo($user, $proposicao)
            && $user->hasPermission('proposicoes:restaurar');
    }

    public function forceDelete(User $user, Proposicao $proposicao): bool
    {
        return false;
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, Proposicao $proposicao): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $proposicao->camara_id;
    }
}
