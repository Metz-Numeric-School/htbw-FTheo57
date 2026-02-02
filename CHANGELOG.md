**Failles fixées**

Modifications du fichier : `config/routes.json`

Ajout des guards `AdminGuard` sur les routes `/admin/user` et `/admin/user/new`

Ajout des guards `UserGuard` sur les routes membres `/habits` et `/habits/create`

Les utilisateurs qui ne sont pas admins ne peuvent plus accéder aux interfaces administrateur

_Mots de passe stockés en clair_

Implémentation du hachage des mots de passe avec `password_hash()` lors de l'insertion
Utilisation de `password_verify()` pour la vérification lors de la connexion

Modifications des fichiers :
`src/Repository/UserRepository.php`
`src/Controller/SecurityController.php`

Les mots de passe sont directement sécurisés dans la DB

_**Injections XSS**_

Ajout de `htmlspecialchars()` sur toutes les variables affichées

`templates/admin/user/new.html.php`
`templates/admin/user/index.html.php`
`templates/register/index.html.php`
`templates/member/dashboard/index.html.php`

Protection contre l'injection JS

**Injection SQL lors de la création d'habitudes**

Fichiers modifiés : `src/Repository/HabitRepository.php`

Remplacement de la concaténation directe par des requêtes préparées avec paramètres nommés
Utilisation de `execute()` avec un tableau de paramètres sécurisés
Impact : Protection contre les attaques par injection SQL

**Bugs fixés**

Fatal error sur `/api/habits`

Dans : `src/Controller/Api/HabitsController.php`

Correction de la déclaration de classe (héritage de `AbstractController`)

L'API retourne les habitudes en format JSOn

_Redirection invalide après inscription_

Fichiers modifiés : `src/Controller/RegisterController.php`

Correction de la redirection vers `/dashboard` au lieu de `/user/tickets`

