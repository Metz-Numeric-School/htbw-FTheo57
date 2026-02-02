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

**Injections SQL supplémentaires corrigées**

Remplacement de toutes les concaténations directes par des requêtes préparées dans les repositories

Fichiers modifiés :
`src/Repository/UserRepository.php` (méthodes `find` et `findByEmail`)
`src/Repository/HabitRepository.php` (méthodes `find` et `findByUser`)
`src/Repository/HabitLogRepository.php` (méthode `findByHabit`)

Protection complète contre les injections SQL dans toutes les requêtes

**Faille d'autorisation - Modification d'habitudes**

Fichier modifié : `src/Controller/Member/HabitsController.php`

Ajout d'une vérification que l'habitude appartient à l'utilisateur connecté avant modification dans la méthode `toggle`

Les utilisateurs ne peuvent plus modifier les habitudes d'autres utilisateurs

**Faille d'autorisation - API expose toutes les habitudes**

Fichier modifié : `src/Controller/Api/HabitsController.php`

L'API retourne maintenant uniquement les habitudes de l'utilisateur connecté au lieu de toutes les habitudes

**XSS supplémentaires corrigés**

Ajout de `htmlspecialchars()` sur les variables manquantes dans les templates

Fichiers modifiés :
`templates/admin/user/index.html.php` (échappement du nom de famille)
`templates/member/dashboard/index.html.php` (échappement des noms et descriptions d'habitudes)

**Bugs corrigés**

_Erreur fatale lors de la connexion admin_

Fichier modifié : `src/Controller/SecurityController.php`

Correction de l'appel à `getIsAdmin()` en `getIsadmin()` pour correspondre à la méthode de l'entité User

Ajout de `firstname` dans la session utilisateur pour éviter les erreurs dans les templates

Correction de la logique de redirection avec ajout de `exit` manquant

_Redirection 404 après création d'habitude_

Fichier modifié : `src/Controller/Member/HabitsController.php`

Correction de la redirection vers `/habits` au lieu de `/habit` qui n'existe pas

_Nom de classe incorrect dans l'API_

Fichier modifié : `src/Controller/Api/HabitsController.php`

Correction du nom de classe de `HabitController` en `HabitsController` pour correspondre à la route

_Validation d'email manquante_

Fichiers modifiés :
`src/Controller/RegisterController.php`
`src/Controller/Admin/UserController.php`

Ajout de la validation du format d'email avec `filter_var`

Ajout de la vérification d'unicité de l'email avant insertion
