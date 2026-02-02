# Liste Complète des Bugs et Failles de Sécurité

## FAILLES DE SÉCURITÉ

### 1. Injection SQL dans `UserRepository::findByEmail()`

**Fichier à modifier :** `src/Repository/UserRepository.php`

**Ligne concernée :** 22-26

**Code actuel :**

```php
public function findByEmail(string $email)
{
    $sql = "SELECT * FROM mns_user WHERE email = '$email'";
    $query = $this->getConnection()->query($sql);
    return EntityMapper::map(User::class, $query->fetch());
}
```

**Modification à apporter :**

```php
public function findByEmail(string $email)
{
    $sql = "SELECT * FROM mns_user WHERE email = :email";
    $query = $this->getConnection()->prepare($sql);
    $query->execute(['email' => $email]);
    return EntityMapper::map(User::class, $query->fetch());
}
```

**Impact :** Injection SQL possible lors de la connexion via le champ email

---

### 2. Injection SQL dans `HabitRepository::find()`

**Fichier à modifier :** `src/Repository/HabitRepository.php`

**Ligne concernée :** 16-20

**Code actuel :**

```php
public function find(int $id)
{
    $habit = $this->getConnection()->query("SELECT * FROM habits WHERE id = $id");
    return EntityMapper::map(Habit::class, $habit->fetch());
}
```

**Modification à apporter :**

```php
public function find(int $id)
{
    $sql = "SELECT * FROM habits WHERE id = :id";
    $query = $this->getConnection()->prepare($sql);
    $query->execute(['id' => $id]);
    return EntityMapper::map(Habit::class, $query->fetch());
}
```

**Impact :** Injection SQL possible si cette méthode est utilisée avec des données utilisateur

---

### 3. Injection SQL dans `UserRepository::find()`

**Fichier à modifier :** `src/Repository/UserRepository.php`

**Ligne concernée :** 16-20

**Code actuel :**

```php
public function find(int $id)
{
    $user = $this->getConnection()->query("SELECT * FROM mns_user WHERE id = $id");
    return EntityMapper::map(User::class, $user);
}
```

**Modification à apporter :**

```php
public function find(int $id)
{
    $sql = "SELECT * FROM mns_user WHERE id = :id";
    $query = $this->getConnection()->prepare($sql);
    $query->execute(['id' => $id]);
    return EntityMapper::map(User::class, $query->fetch());
}
```

**Impact :** Injection SQL possible

---

### 4. Injection SQL dans `HabitRepository::findByUser()`

**Fichier à modifier :** `src/Repository/HabitRepository.php`

**Ligne concernée :** 22-27

**Code actuel :**

```php
public function findByUser(int $userId)
{
    $sql = "SELECT * FROM habits WHERE user_id = $userId";
    $query = $this->getConnection()->query($sql);
    return EntityMapper::mapCollection(Habit::class, $query->fetchAll());
}
```

**Modification à apporter :**

```php
public function findByUser(int $userId)
{
    $sql = "SELECT * FROM habits WHERE user_id = :user_id";
    $query = $this->getConnection()->prepare($sql);
    $query->execute(['user_id' => $userId]);
    return EntityMapper::mapCollection(Habit::class, $query->fetchAll());
}
```

**Impact :** Injection SQL possible si l'ID utilisateur peut être manipulé

---

### 5. Injection SQL dans `HabitLogRepository::findByHabit()`

**Fichier à modifier :** `src/Repository/HabitLogRepository.php`

**Ligne concernée :** 25-30

**Code actuel :**

```php
public function findByHabit(int $habitId)
{
    $sql = "SELECT * FROM habit_logs WHERE habit_id = $habitId ORDER BY log_date DESC";
    $query = $this->getConnection()->query($sql);
    return EntityMapper::mapCollection(HabitLog::class, $query->fetchAll());
}
```

**Modification à apporter :**

```php
public function findByHabit(int $habitId)
{
    $sql = "SELECT * FROM habit_logs WHERE habit_id = :habit_id ORDER BY log_date DESC";
    $query = $this->getConnection()->prepare($sql);
    $query->execute(['habit_id' => $habitId]);
    return EntityMapper::mapCollection(HabitLog::class, $query->fetchAll());
}
```

**Impact :** Injection SQL possible

---

### 6. Faille d'autorisation - Modification d'habitudes d'autres utilisateurs

**Fichier à modifier :** `src/Controller/Member/HabitsController.php`

**Ligne concernée :** 68-78 (méthode `toggle()`)

**Code actuel :**

```php
public function toggle()
{
    if (!empty($_POST['habit_id'])) {
        $habitId = (int)$_POST['habit_id'];
        $this->habitLogRepository->toggleToday($habitId);
    }

    header('Location: /dashboard');
    exit;
}
```

**Modification à apporter :**

```php
public function toggle()
{
    if (!empty($_POST['habit_id'])) {
        $habitId = (int)$_POST['habit_id'];
        $userId = $_SESSION['user']['id'];

        // Vérifier que l'habitude appartient à l'utilisateur connecté
        $habit = $this->habitRepository->find($habitId);
        if ($habit && $habit->getUserId() === $userId) {
            $this->habitLogRepository->toggleToday($habitId);
        } else {
            // Habitude non trouvée ou n'appartient pas à l'utilisateur
            http_response_code(403);
            header('Location: /habits');
            exit;
        }
    }

    header('Location: /dashboard');
    exit;
}
```

**Impact :** Un utilisateur peut modifier les habitudes d'autres utilisateurs en envoyant un `habit_id` différent

---

### 7. Faille d'autorisation - API expose toutes les habitudes

**Fichier à modifier :** `src/Controller/Api/HabitsController.php`

**Ligne concernée :** 17-22 (méthode `index()`)

**Code actuel :**

```php
public function index()
{
    return $this->json([
        'habits' => $this->habitRepository->findAll()
    ]);
}
```

**Modification à apporter :**

```php
public function index()
{
    $userId = $_SESSION['user']['id'];
    return $this->json([
        'habits' => $this->habitRepository->findByUser($userId)
    ]);
}
```

**Impact :** Un utilisateur peut voir les habitudes de tous les autres utilisateurs

---

### 8. XSS - Échappement manquant dans le template admin

**Fichier à modifier :** `templates/admin/user/index.html.php`

**Ligne concernée :** 29

**Code actuel :**

```php
<td><?php echo htmlspecialchars($user->getFirstname()) ?> <?php echo $user->getLastname() ?></td>
```

**Modification à apporter :**

```php
<td><?php echo htmlspecialchars($user->getFirstname()) ?> <?php echo htmlspecialchars($user->getLastname()) ?></td>
```

**Impact :** Injection XSS possible si le nom de famille contient du code malveillant

---

### 9. XSS - Échappement manquant dans le dashboard membre

**Fichier à modifier :** `templates/member/dashboard/index.html.php`

**Lignes concernées :** 50-51

**Code actuel :**

```php
<h5 class="card-title"><?= $habit->getName() ?></h5>
<p class="card-text"><?= $habit->getDescription() ?></p>
```

**Modification à apporter :**

```php
<h5 class="card-title"><?= htmlspecialchars($habit->getName()) ?></h5>
<p class="card-text"><?= htmlspecialchars($habit->getDescription()) ?></p>
```

**Impact :** Injection XSS possible via les noms/descriptions d'habitudes

---

### 10. Variable de session potentiellement non définie

**Fichier à modifier :** `templates/member/dashboard/index.html.php` et `src/Controller/SecurityController.php`

**Ligne concernée dans template :** 4
**Ligne concernée dans controller :** 35-38

**Code actuel dans template :**

```php
<h1 class="mb-4">Bonjour <?= htmlspecialchars($_SESSION['user']['firstname']) ?> !</h1>
```

**Code actuel dans SecurityController :**

```php
$_SESSION['user'] = [
    'id' => $user->getId(),
    'username' => $user->getFirstname(),
];
```

**Modification à apporter dans SecurityController :**

```php
$_SESSION['user'] = [
    'id' => $user->getId(),
    'username' => $user->getFirstname(),
    'firstname' => $user->getFirstname(),
];
```

**Modification à apporter dans template (optionnel, pour plus de sécurité) :**

```php
<h1 class="mb-4">Bonjour <?= htmlspecialchars($_SESSION['user']['firstname'] ?? $_SESSION['user']['username'] ?? 'Utilisateur') ?> !</h1>
```

**Impact :** Erreur PHP si la variable n'existe pas (dans `SecurityController::login()`, seule `username` est définie, pas `firstname`)

---

## BUGS

### 11. Redirection 404 après création d'habitude

**Fichier à modifier :** `src/Controller/Member/HabitsController.php`

**Ligne concernée :** 55

**Code actuel :**

```php
header('Location: /habit');
```

**Modification à apporter :**

```php
header('Location: /habits');
```

**Impact :** Erreur 404 après création d'une habitude (la route `/habit` n'existe pas, il faut `/habits`)

---

### 12. Erreur de méthode - Appel à une méthode inexistante

**Fichier à modifier :** `src/Controller/SecurityController.php`

**Ligne concernée :** 41

**Code actuel :**

```php
$_SESSION['admin'] = $user->getIsAdmin();
```

**Modification à apporter :**

```php
$_SESSION['admin'] = $user->getIsadmin();
```

**Impact :** Fatal error lors de la connexion d'un admin (la méthode s'appelle `getIsadmin()` avec minuscule, pas `getIsAdmin()`)

---

### 13. Validation d'email manquante

**Fichiers à modifier :**

- `src/Controller/RegisterController.php`
- `src/Controller/Admin/UserController.php`

**Lignes concernées :**

- RegisterController : après la ligne 32
- UserController : après la ligne 41

**Modification à apporter dans RegisterController :**

```php
if(empty($user['email']))
    $errors['email'] = 'L\'email est obligatoire';
elseif(!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
    $errors['email'] = 'L\'email n\'est pas valide';
```

**Modification à apporter dans UserController :**

```php
if(empty($user['email']))
    $errors['email'] = 'L\'email est obligatoire';
elseif(!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
    $errors['email'] = 'L\'email n\'est pas valide';
```

**Impact :** Des emails invalides peuvent être enregistrés

---

### 14. Pas de vérification d'unicité d'email

**Fichiers à modifier :**

- `src/Controller/RegisterController.php`
- `src/Controller/Admin/UserController.php`

**Lignes concernées :**

- RegisterController : après la validation de l'email (après ligne 32)
- UserController : après la validation de l'email (après ligne 41)

**Modification à apporter dans RegisterController :**

```php
if(empty($user['email']))
    $errors['email'] = 'L\'email est obligatoire';
elseif(!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
    $errors['email'] = 'L\'email n\'est pas valide';
elseif($this->userRepository->findByEmail($user['email']))
    $errors['email'] = 'Cet email est déjà utilisé';
```

**Modification à apporter dans UserController :**

```php
if(empty($user['email']))
    $errors['email'] = 'L\'email est obligatoire';
elseif(!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
    $errors['email'] = 'L\'email n\'est pas valide';
elseif($this->userRepository->findByEmail($user['email']))
    $errors['email'] = 'Cet email est déjà utilisé';
```

**Impact :** Un utilisateur peut s'inscrire avec un email déjà existant, causant une erreur SQL ou un doublon

---

### 15. Problème de logique dans SecurityController::login()

**Fichier à modifier :** `src/Controller/SecurityController.php`

**Ligne concernée :** 20-23

**Code actuel :**

```php
if(!empty($_SESSION['user']))
{
    $_SESSION['admin'] ? header('Location: /admin/dashboard') : header('Location: /dashboard');
}
```

**Modification à apporter :**

```php
if(!empty($_SESSION['user']))
{
    if(!empty($_SESSION['admin'])) {
        header('Location: /admin/dashboard');
    } else {
        header('Location: /dashboard');
    }
    exit;
}
```

**Impact :** Redirection incorrecte et potentiellement deux headers envoyés (manque `exit`)

---

### 16. Nom de classe incorrect dans l'API

**Fichier à modifier :** `src/Controller/Api/HabitsController.php`

**Ligne concernée :** 8

**Code actuel :**

```php
class HabitController extends AbstractController
```

**Modification à apporter :**

```php
class HabitsController extends AbstractController
```

**Impact :** Erreur fatale lors de l'accès à `/api/habits` (la route pointe vers `HabitsController` mais la classe s'appelle `HabitController`)

---

## RÉSUMÉ DES FICHIERS À MODIFIER

### Fichiers de Repository (5 fichiers)

1. `src/Repository/UserRepository.php` - 3 modifications (findByEmail, find)
2. `src/Repository/HabitRepository.php` - 2 modifications (find, findByUser)
3. `src/Repository/HabitLogRepository.php` - 1 modification (findByHabit)

### Fichiers de Controller (4 fichiers)

4. `src/Controller/Member/HabitsController.php` - 2 modifications (toggle, new)
5. `src/Controller/Api/HabitsController.php` - 2 modifications (nom de classe, index)
6. `src/Controller/SecurityController.php` - 2 modifications (login, getIsAdmin)
7. `src/Controller/RegisterController.php` - 2 modifications (validation email, unicité email)
8. `src/Controller/Admin/UserController.php` - 2 modifications (validation email, unicité email)

### Fichiers de Template (2 fichiers)

9. `templates/admin/user/index.html.php` - 1 modification (XSS)
10. `templates/member/dashboard/index.html.php` - 2 modifications (XSS, variable session)

---

## STATISTIQUES

- **Total de failles de sécurité :** 10
- **Total de bugs fonctionnels :** 6
- **Total de problèmes :** 16
- **Total de fichiers à modifier :** 10 fichiers

---

## PRIORITÉS

### Critique (à corriger immédiatement)

1. Injection SQL dans `findByEmail()` - Utilisée lors de la connexion
2. Faille d'autorisation dans `toggle()` - Permet de modifier les habitudes d'autrui
3. Erreur fatale dans `SecurityController::login()` - Empêche la connexion admin
4. Erreur fatale dans l'API - Empêche l'accès à `/api/habits`

### Haute priorité

5. Toutes les autres injections SQL
6. API expose toutes les habitudes
7. XSS dans les templates

### Moyenne priorité

8. Validation d'email
9. Vérification d'unicité d'email
10. Redirection 404
11. Variable de session manquante
