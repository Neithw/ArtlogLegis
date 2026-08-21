<?php

namespace App\Policies;

use App\Models\ItemPauta;
use App\Models\User;
use App\Models\Votacao;

class VotacaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->possuiCamara($user)
            && $user->hasPermission('votacoes:visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Votacao $votacao): bool
    {
        return $this->votacaoPertenceAoEscopo($user, $votacao)
            && $user->hasPermission('votacoes:visualizar');
    }

    public function abrir(User $user, ItemPauta $itemPauta): bool
    {
        return $this->itemPertenceAoEscopo($user, $itemPauta)
            && $user->hasPermission('votacoes:abrir')
            && $itemPauta->situacao === 'pendente'
            && $itemPauta->sessao->situacao === 'aberta';
    }

    public function registrarVoto(User $user, Votacao $votacao): bool
    {
        return $this->votacaoPertenceAoEscopo($user, $votacao)
            && $user->hasPermission('votacoes:registrar-votos')
            && $votacao->situacao === 'aberta'
            && $votacao->itemPauta->sessao->situacao === 'aberta';
    }

    public function encerrar(User $user, Votacao $votacao): bool
    {
        return $this->votacaoPertenceAoEscopo($user, $votacao)
            && $user->hasPermission('votacoes:encerrar')
            && $votacao->situacao === 'aberta'
            && $votacao->itemPauta->sessao->situacao === 'aberta';
    }

    public function cancelar(User $user, Votacao $votacao): bool
    {
        return $this->votacaoPertenceAoEscopo($user, $votacao)
            && $user->hasPermission('votacoes:cancelar')
            && $votacao->situacao === 'aberta'
            && $votacao->itemPauta->sessao->situacao === 'aberta';
    }

    private function possuiCamara(User $user): bool
    {
        return $user->camara_id !== null;
    }

    private function itemPertenceAoEscopo(User $user, ItemPauta $itemPauta): bool
    {
        return $this->possuiCamara($user)
            && (int) $user->camara_id === (int) $itemPauta->sessao->camara_id;
    }

    private function votacaoPertenceAoEscopo(User $user, Votacao $votacao): bool
    {
        return $this->itemPertenceAoEscopo($user, $votacao->itemPauta);
    }
}
