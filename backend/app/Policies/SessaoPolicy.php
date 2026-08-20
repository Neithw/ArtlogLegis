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

    public function convocar(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:convocar')
            && $sessao->situacao === 'em_preparacao';
    }

    public function abrir(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:abrir')
            && $sessao->situacao === 'convocada';
    }

    public function suspender(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:suspender')
            && $sessao->situacao === 'aberta';
    }

    public function retomar(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:retomar')
            && $sessao->situacao === 'suspensa';
    }

    public function encerrar(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:encerrar')
            && $sessao->situacao === 'aberta';
    }

    public function cancelar(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:cancelar')
            && in_array(
                $sessao->situacao,
                ['em_preparacao', 'convocada'],
                true
            );
    }

    public function gerenciarPauta(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:gerenciar-pauta')
            && $sessao->situacao === 'em_preparacao';
    }

    public function gerenciarPresencas(User $user, Sessao $sessao): bool
    {
        return $this->pertenceAoEscopo($user, $sessao)
            && $user->hasPermission('sessoes:gerenciar-presencas')
            && in_array(
                $sessao->situacao,
                ['convocada', 'aberta', 'suspensa'],
                true
            );
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
