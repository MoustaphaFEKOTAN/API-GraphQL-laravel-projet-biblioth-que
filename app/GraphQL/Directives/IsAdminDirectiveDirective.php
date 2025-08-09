<?php declare(strict_types=1);

namespace App\GraphQL\Directives;

use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;

final class IsAdminDirectiveDirective extends BaseDirective implements FieldManipulator
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @isAdminDirective on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Manipulate the AST based on a field definition.
     */
    public function manipulateFieldDefinition(
        DocumentAST &$documentAST,
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode &$parentType
    ): void {
        // TODO implement the field manipulator
    }
}
