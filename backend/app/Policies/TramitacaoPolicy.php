<?php

namespace App\Policies;

use App\Models\Tramitacao;
use App\Models\User;

class TramitacaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('tramitacoes:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tramitacao $tramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $tramitacao)
            && $user->hasPermission('tramitacoes:visualizar');
    }

    public function receber(User $user, Tramitacao $tramitacao): bool
    {
        return $this->pertenceAoEscopo($user, $tramitacao)
            && $user->hasPermission('tramitacoes:receber');
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function pertenceAoEscopo(User $user, Tramitacao $tramitacao): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $tramitacao->proposicao->camara_id;
    }
}
