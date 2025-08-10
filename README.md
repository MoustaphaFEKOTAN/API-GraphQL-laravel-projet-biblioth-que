📚 Description
Cette API GraphQL est construite avec Laravel 11 et Lighthouse. Elle permet la gestion complète des entités principales comme les livres, utilisateurs, rôles, catégories, avec :

Authentification sécurisée via Laravel Sanctum.

Autorisations fines avec Policies Laravel.

Mutations & Queries optimisées.

Pagination flexible (classique ou simple).

Gestion des emails (vérification, reset mot de passe, etc.).

⚙️ Installation
Cloner le dépôt :

bash
Modifier
git clone https://github.com/MoustaphaFEKOTAN/API-GraphQL-laravel-projet-biblioth-que.git
cd tonrepo
Installer les dépendances :

bash
Modifier
composer install
Copier le fichier .env et configurer la base de données et autres variables :

bash
Modifier
cp .env.example .env
Générer la clé d’application :

bash
Modifier
php artisan key:generate
Lancer les migrations et seeders :

bash
Modifier
php artisan migrate --seed
(Optionnel) Pour tester , utuliser Postman ou /graphql-playground:

 - composer require mll-lab/laravel-graphql-playground
 - php artisan vendor:publish --provider="MLL\GraphQLPlayground\GraphQLPlaygroundServiceProvider"

🚀 Utilisation
Point d’entrée GraphQL pour tester votre schéma
L’endpoint principal est /graphql-playground.

Authentification
Utilise Laravel Sanctum pour gérer les tokens d’authentification.

Toutes les mutations et queries protégées nécessitent le header HTTP :

makefile
Modifier
Authorization: Bearer <token>
📖 Schéma GraphQL
Quelques exemples importants :

Requête de livres avec pagination

graphql
Copier
Modifier
query {
  livres(first: 10, page: 1) {
    data {
      id
      titre
      description
    }
    paginatorInfo {
      currentPage
      hasMorePages
    }
  }
}
Mutation mise à jour livre (avec autorisation)

graphql
Copier
Modifier
mutation {
  updateLivre(
    id: 1,
    titre: "Nouveau titre"
  ) {
    id
    titre
  }
}
Mutation inscription / connexion / déconnexion / gestion email
(voir les mutations documentées dans le schéma)

🔐 Sécurité & Permissions
Utilisation des policies Laravel pour vérifier les droits sur les mutations sensibles (ex : mise à jour, suppression).

Utilisation de la directive @can pour automatiser les contrôles basés sur la clé primaire id.

Protection des mutations et queries via la directive @guard avec auth:sanctum.

🧪 Tests
Les tests unitaires sont basés sur PHPUnit et Features.

Exemple de lancement des tests :

bash
php artisan test

📦 Packages principaux
Laravel 11

Lighthouse

Laravel Sanctum & Laravel Fortify

...

🔄 Contribution
Les contributions sont les bienvenues, merci de respecter la structure et de faire des pull requests.

📞 Contact
Pour toute question, contactez moustaphafek@gmail.com ].
