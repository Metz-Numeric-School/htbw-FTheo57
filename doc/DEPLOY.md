# Procédure de Déploiement

Décrivez ci-dessous votre procédure de déploiement en détaillant chacune des étapes. De la préparation du VPS à la méthodologie de déploiement continu.

## Préparation du VPS

**Pour préparer le VPS :**

J'ouvre une invite de commande CMD, puis je me connecte à la VM à l'aide de la commande `ssh root@192.168.23.140`

Une fois connecté à la VM, je mets à jour Debian.

`apt update && apt upgrade-y`

**Je prépare mon espace aaPanel**

Je me rend sur le site https://www.aapanel.com/.

Puis dans l'onglet installation, je copie colle la commande afin de l'installer sur ma VM.

`URL=https://www.aapanel.com/script/install_7.0_en.sh && if [ -f /usr/bin/curl ];then curl -ksSO "$URL" ;else wget --no-check-certificate -O install_7.0_en.sh "$URL";fi;bash install_7.0_en.sh ipssl`

Une fois l'installation terminée je récupère les identifiants ainsi que l'URL de connexion au panel.

Une fois connecté sur aaPanel, j'installe les dépendances recommandées.

## Méthode de déploiement

**Configuration du site web dans aaPanel :**

Je me connecte à aaPanel et je vais dans l'onglet **Website**.

Je clique sur **Add Site** pour créer un nouveau site web.

Je configure le domaine (par exemple `foucault.dfs.lan`) et je m'assure que le **Document Root** pointe vers le dossier `public` de mon projet :

- Chemin complet : `/www/wwwroot/foucault.dfs.lan/public`
- Le Document Root doit absolument pointer vers le dossier `public` et non vers la racine du projet

Je sélectionne PHP 8.3 dans les options et je crée le site.

**Configuration de la base de données :**

Je vais dans l'onglet **Database** → **MySQL**.

Je crée une nouvelle base de données nommée `habit_database`.

Je crée un nouvel utilisateur MySQL :

- Nom d'utilisateur : `habit_user`
- Mot de passe : `azertyuiop`
- J'accorde tous les privilèges sur la base `habit_database`

Je clique sur **phpMyAdmin** pour ouvrir l'interface d'administration.

Je sélectionne la base `habit_database` et j'importe le fichier `database.sql` via l'onglet **Importer**.

**Upload des fichiers du projet :**

Je me connecte en SSH à la VM et je me place dans le répertoire du site web :
`cd /www/wwwroot/foucault.dfs.lan/`

Je clone ou je transfère les fichiers du projet dans ce répertoire.

Je m'assure que tous les fichiers sont bien présents, notamment le dossier `public` et le fichier `.htaccess` dans `public`.

**Configuration du fichier .env :**

Je modifie le fichier `.env` pour correspondre à ma configuration :

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=habit_database
DB_USERNAME=habit_user
DB_PASSWORD=azertyuiop
```

**Vérification et permissions :**

Je vérifie que les permissions sont correctes sur les fichiers :
`chmod -R 755 /www/wwwroot/foucault.dfs.lan/`

Je vérifie que le fichier `.htaccess` est bien présent dans le dossier `public` et que la réécriture d'URL est activée.

Je teste l'accès au site via le navigateur et je vérifie que :

- La page d'accueil s'affiche correctement
- Les images se chargent (chemin `/images/logo.png`)
- Les liens fonctionnent (pas d'erreur 404)
- La connexion à la base de données fonctionne

**Redémarrage des services si nécessaire :**

Si des modifications de configuration sont nécessaires, je redémarre PHP et Nginx depuis aaPanel dans l'onglet **App Store** → **Installed**.
