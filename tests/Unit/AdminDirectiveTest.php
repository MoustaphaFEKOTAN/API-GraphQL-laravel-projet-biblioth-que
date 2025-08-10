<?php

namespace Tests\Unit\GraphQL;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use App\GraphQL\Directives\IsAdminDirective;

class AdminDirectiveTest extends TestCase
{
    public function test_it_throws_if_user_not_authenticated()
    {
        // Simuler aucun utilisateur connecté
        Auth::shouldReceive('user')->andReturn(null);

        $directive = new IsAdminDirective();

        $fieldValue = $this->createMock(FieldValue::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Accès refusé. Vous devez être un administrateur.');

        $directive->handleField($fieldValue);
    }

    public function test_it_throws_if_user_not_admin()
    {
        $user = (object) ['is_admin' => false];
        Auth::shouldReceive('user')->andReturn($user);

        $directive = new IsAdminDirective();

        $fieldValue = $this->createMock(FieldValue::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Accès refusé. Vous devez être un administrateur.');

        $directive->handleField($fieldValue);
    }

    public function test_it_allows_if_user_is_admin()
    {
        $user = (object) ['is_admin' => true];
        Auth::shouldReceive('user')->andReturn($user);

        $directive = new IsAdminDirective();

        $fieldValue = $this->createMock(FieldValue::class);

        // Ne doit rien lancer
        $directive->handleField($fieldValue);

        $this->assertTrue(true); // Juste pour marquer le test comme réussi
    }
}
