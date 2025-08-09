<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Livre;

class LivrePolicy
{
    // Vérifie si l'utilisateur peut supprimer un livre
    public function delete(User $user, Livre $livre): bool
    {
        return $user->id === $livre->user_id;
    }

    // Vérifie si l'utilisateur peut modifier un livre
    public function update(User $user, Livre $livre): bool
    {
        return $user->id === $livre->user_id;
    }
}
