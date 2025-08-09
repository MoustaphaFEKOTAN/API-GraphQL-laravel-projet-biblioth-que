<?php declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Models\Livre;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

final class IsLivreeownerDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @isLivreeowner on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Handle the field middleware.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $fieldValue
     * @return void
     */
   public function handleField(FieldValue $fieldValue): void
{
    $originalResolver = $fieldValue->getResolver();

    $fieldValue->setResolver(function ($root, array $args, $context, $resolveInfo) use ($originalResolver) {
        $user = Auth::user();

        $slug = $args['slug'] ?? null;

        if (!$slug) {
            throw new AuthorizationException('Le slug du livre est requis.');
        }

        $livre = \App\Models\Livre::where('slug', $slug)->first();

        if (!$livre) {
            throw new AuthorizationException('Livre non trouvé.');
        }

        if ($livre->user_id !== $user->id) {
            throw new AuthorizationException('Accès refusé : vous n\'êtes pas le propriétaire de ce livre.');
        }

        return $originalResolver($root, $args, $context, $resolveInfo);
    });
}
}