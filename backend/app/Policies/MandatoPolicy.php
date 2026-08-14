<?php

namespace App\Policies;

use App\Models\Mandato;
use App\Models\User;

class MandatoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('mandatos:visualizar');
    }

    public function view(User $user, Mandato $mandato): bool
    {
        return $user->hasPermission('mandatos:visualizar')
            && $this->pertenceAoEscopo($user, $mandato);
    }

    public function viewArchived(User $user): bool
    {
        return $user->hasPermission('mandatos:restaurar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('mandatos:criar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mandato $mandato): bool
    {
        return $user->hasPermission('mandatos:editar')
            && $this->pertenceAoEscopo($user, $mandato);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mandato $mandato): bool
    {
        return $user->hasPermission('mandatos:excluir')
            && $this->pertenceAoEscopo($user, $mandato);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mandato $mandato): bool
    {
        return $user->hasPermission('mandatos:restaurar')
            && $this->pertenceAoEscopo($user, $mandato);
    }

    private function pertenceAoEscopo(User $user, Mandato $mandato): bool
    {
        return (int) $user->camara_id === (int) $mandato->legislatura->camara_id;
    }
}
