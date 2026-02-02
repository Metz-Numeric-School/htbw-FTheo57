# Questions

Répondez ici aux questions théoriques en détaillant un maxium vos réponses :

1. Expliquer la procédure pour réserver un nom de domaine chez OVH avec des captures d'écran (arrêtez-vous au paiement) :

Pour réserver un nom de domaine sur OVH il faut tout d'abord se rendre sur le site : https://www.ovhcloud.com/fr
Une fois sur le site, dans la barre de navigation, on peut noter le nom de domaine que l'on souhaite réserver.
![Texte alternatif](/doc/image/achatOvh.png "Titre de l'image")
Sélectionner le nom de domaine souhaité.
![Texte alternatif](/doc/image/achatOvh2.png "Titre de l'image")
Si l'on possède déjà un hébergement sur OVH nous pouvons le lier directement au nom de domaine souhaité.
![Texte alternatif](/doc/image/achatOvh3.png "Titre de l'image")
Pour terminer procéder au paiement pour obtenir le nom de domaine.

2. Comment faire pour qu'un nom de domaine pointe vers une adresse IP spécifique ?
   Il faut aller sur l'hébergeur choisit.
   Se rendre dans la zone DNS de l'hébergeur.
   Ensuite il va falloir rediriger le nom de domaine vers l'adresse IP spécifique

3. Comment mettre en place un certificat SSL ?

   Méthode avec aaPanel :

   - Dans aaPanel, aller dans **Website** puis ouvrir les **Settings** du site concerné.
   - Cliquer sur l'onglet **SSL**.
   - Choisir **Let's Encrypt** : saisir une adresse e-mail valide, cocher le ou les noms de domaine (ex. `foucault.dfs.lan`dans le cas de ce projet).
   - Cliquer sur **Apply**: aaPanel génère et installe automatiquement le certificat.
   - Activer **Force HTTPS** si on souhaite rediriger tout le trafic HTTP vers HTTPS.
