<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Livre;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class LivreMutator
{
    public function create($root, array $args)
    {
        $user = Auth::user();
    
        // ✅ Validation des champs
        $validated = validator($args, [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date_sortie' => ['required', 'date'],
            'categorie_id' => ['required', 'exists:categories,id'],
            'cover' => ['nullable', 'file', 'image', 'max:5120'], // max 5MB
        ])->validate();
    
        // ✅ Gestion de l'upload si cover fournie
        $coverPath = null;
        if (isset($validated['cover'])) {
            $coverPath = $validated['cover']->store('covers', 'public');
        }
    
        // ✅ Création du livre avec tous les champs
        $livre = Livre::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'date_sortie' => $validated['date_sortie'],
            'categorie_id' => $validated['categorie_id'],
            'user_id' => $user->id,
            'cover' => $coverPath,
        ]);
    
        
        return $livre;
    }
    

   public function updateBySlug($root, array $args)
{
    $livre = Livre::where('slug', $args['slug'])->firstOrFail();

    // Vérifier la policy manuellement
    if (!\Illuminate\Support\Facades\Gate::allows('update', $livre)) {
        throw new AuthorizationException("Accès refusé");
    }

     // Gérer l'upload de cover
    if (!empty($args['cover'])) {
        // Supprimer l'ancienne cover si elle existe
        if ($livre->cover) {
            Storage::disk('public')->delete($livre->cover);
        }
        $livre->cover = $args['cover']->store('covers', 'public');
    }

    // Mettre à jour les autres champs
    $livre->fill(array_filter($args, fn($value, $key) => !in_array($key, ['slug','cover']), ARRAY_FILTER_USE_BOTH));
    $livre->save();
    return $livre;
}

public function deleteBySlug($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfoe)
{

 $livre = Livre::where('slug', $args['slug'])->firstOrFail();

    $livre->delete();
 

    return $livre;
}

}