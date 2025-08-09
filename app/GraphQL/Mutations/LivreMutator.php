<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Livre;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

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

    public function updateBySlug($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfoe)
    {
      
  $livre = Livre::find($args['id']);

        $livre->fill(array_filter($args, fn($value, $key) => $key !== 'id', ARRAY_FILTER_USE_BOTH));
        $livre->save();

        return $livre;
    }
public function deleteBySlug($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfoe)
{

  $livre = Livre::find($args['id']);

    $livre->delete();
 

    return $livre;
}

}