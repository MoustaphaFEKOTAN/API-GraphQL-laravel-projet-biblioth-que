<?php

namespace Tests\Feature\GraphQL;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class CreateLivreTest extends TestCase
{
     use RefreshDatabase;
    use MakesGraphQLRequests;

    public function test_un_utilisateur_authentifie_peut_creer_un_livre()
    {
        // 1. Créer un utilisateur
       $role = Role::factory()->create([
    'nom' =>'auteur',
]);

        // Création utilisateur pour authentification
       $user= User::factory()->create([
            'role_id' => $role->id,
        ]);

        // 2. Créer une catégorie
        $categorie = Categorie::factory()->create();

        // 3. Définir la mutation GraphQL
        $mutation = '
            mutation createLivre($titre: String!, $description: String!, $date_sortie: String!, $categorie_id: ID!) {
                create(titre: $titre, description: $description, date_sortie: $date_sortie, categorie_id: $categorie_id) {
                    id
                    titre
                    description
                    date_sortie
                    categorie_id
                    user_id
                }
            }
        ';

        // 4. Variables à envoyer
        $variables = [
            'titre' => 'Mon Livre Test',
            'description' => 'Description du livre test',
            'date_sortie' => '2025-08-10',
            'categorie_id' => $categorie->id,
        ];

        // 5. Envoyer la requête en étant authentifié
        $response = $this
            ->actingAs($user,'sanctum')
            ->graphQL($mutation, $variables);

            // dump($response->json());

        // 6. Assertions
        $response
            ->assertJson([
                'data' => [
                    'create' => [
                        'titre' => 'Mon Livre Test',
                        'description' => 'Description du livre test',
                        'date_sortie' => '2025-08-10',
                        'categorie_id' =>  $categorie->id,
                        'user_id' =>  $user->id,
                    ]
                ]
            ]);

        // 7. Vérifier en base de données
        $this->assertDatabaseHas('livres', [
            'titre' => 'Mon Livre Test',
            'description' => 'Description du livre test',
            'categorie_id' => $categorie->id,
            'user_id' => $user->id,
        ]);
    }
}
