<?php declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

final class IsAuthorDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @IsAuthorDirective on FIELD_DEFINITION
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
        $user = Auth::user();

       if (Auth::check() && (! $user->role->nom === 'auteur' || ! $user->is_admin)) {
         throw new AuthorizationException('Accès refusé. Vous devez être un auteur.');
    }

        // Pas besoin de retourner quoi que ce soit.
        // Le schéma continue sa construction normalement.
    }
}
