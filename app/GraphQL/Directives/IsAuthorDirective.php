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
directive @isAuthor on FIELD_DEFINITION
GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue): void
    {
        $fieldValue->wrapResolver(function (callable $resolver) {
            return function ($root, array $args, $context, $resolveInfo) use ($resolver) {
                $user = Auth::user();

                if (! $user || ($user->role->nom !== 'auteur' && ! $user->is_admin)) {
                    throw new AuthorizationException(
                        'Accès refusé. Vous devez être un auteur ou un administrateur.'
                    );
                }

                // Appeler le resolver original
                return $resolver($root, $args, $context, $resolveInfo);
            };
        });
    }
}
