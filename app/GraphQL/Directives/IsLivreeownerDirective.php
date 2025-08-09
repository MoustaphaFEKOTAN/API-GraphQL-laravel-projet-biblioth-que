<?php declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

final class IsLivreeownerDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @IsLivreeownerDirective on FIELD_DEFINITION
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
         $user = $request->user();
        $livreslug = $request->input('slug') ?? ($request->input('variables')['slug'] ?? null);

        if (!$livreslug) {
            abort(400, "Livre slug is required.");
        }

        $livre = Livre::find($livreslug);
        if (!$livre) {
            abort(404, "Livre not found.");
        }

        if ($livre->user_slug !== $user->slug) {
            abort(403, "Unauthorized: You are not the owner of this livre.");
        }
    }
}
