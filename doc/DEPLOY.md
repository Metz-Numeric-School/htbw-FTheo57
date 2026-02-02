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

Todo...
