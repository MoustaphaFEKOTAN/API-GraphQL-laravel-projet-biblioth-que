<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Livre;
use Illuminate\Support\Facades\Auth;

class LivreMutator
{
    public function create($root, array $args)
    {
        $user = Auth::user();

        $livre = Livre::create([
            'titre' => $args['titre'],
            'description' => $args['description'],
            'date_sortie' => $args['date_sortie'],
            'categorie_id' => $args['categorie_id'],
            'user_id' => $user->id,
        ]);

        return $livre;
    }

    public function updateBySlug($root, array $args)
    {
        $livre = Livre::where('slug', $args['slug'])->firstOrFail();

        $livre->fill(array_filter($args, fn($value, $key) => $key !== 'slug', ARRAY_FILTER_USE_BOTH));
        $livre->save();

        return $livre;
    }

    public function deleteBySlug($root, array $args)
    {
        $livre = Livre::where('slug', $args['slug'])->firstOrFail();
        $livre->delete();

        return $livre;
    }
}
