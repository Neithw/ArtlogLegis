<?php

namespace App\Policies;

use App\Models\Sessao;
use App\Models\User;

class SessaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('sessoes:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:visualizar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('sessoes:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:editar')
            && $sessao->situacao === 'em_preparacao';
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, Sessao $sessao): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $sessao->camara_id;
    }
}
