# Api_Biblio_Web
Projet d'étude du développement d'une API REST (full) avec Api_platform.

Nous retouvons tous les cas de figures possibles lors du développement d'une API avec Api_platform:

- L'attribution des url suivant les verbes PUT/POST/DELETE/GET pour chacune des entity.
- L'optmisation des Urls via des class Context Builder. ces derniers attribuant des groups supplémentaire suivant le role et la méthode détecté.
- L'utilisation de controllers perso pour géger des statistiques via des urls précis
- L'utilisation d'une série de filtr:  SearchFilter/RangeFilter/OrderFilter/PropertyFilter etc
- L'utilisation de l'EventSubscriberInterface pour faire du traitement avant un flush en BDD 
- La mise en place d'un JWT token via bundle lexik/jwt-authentification
- Les méthodes magiques et ses limites de Doctrine "@hasLifecycleCallback"



Mais aussi sans API_platform:

- Création de controller et d'url personnalisé pour les méthodes PUT/POST/DELETE/GET
- Création de groups et gestion de l'erreur de réference circulaire



La découverte et l'approfondissemnt de plusieurs points technique:

- Les attentes d'une APIFULL REST qui respecte le modèle de Richardson
- Compréhension de la manipulation et traitement des données via la DESERIALISATION/DECODE/ENCODE/SERIALISATION 
- Utilisation avancé de POSTMAN
- Compréhention du certificat SSL/TLS du cryptage symétrique/asymétrique
- Compréhension appronfondie du fichier security.yaml providers/firewall/access_control
- Mise en place de fixtures via bundle et paramétrage sur mesure des champs de chaque entity. 


---
## Procédure d'installation

### 1 - Télécharger et extraire le projet

    wget  https://github.com/AlixRomain/Api_Biblio_Web.git
    
### 2 - Installer les dépendances

    composer install

### 3 - Modifier le fichier .env

* Vos identifiants de base de données

> Ligne 32 : DATABASE_URL=mysql://LOGIN:PASSWORD@127.0.0.1:3306/DATABASENAME?serverVersion=5.7

### 4 - Initialiser la base de données

2 méthodes :

> * Soit utiliser le fichier .sql dans le dossier racine et l'importer dans votre SGBD
> * Soit utiliser les migrations de doctrine et les fixtures


    php bin/console doctrine:migrations:migrate 
    php bin/console doctrine:fixtures:load
