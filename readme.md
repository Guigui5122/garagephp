# Application GaragePHP (projet fromscratch de Mr LOHEZ Christian - Formation STUDI)

## Détails de la création du projet : 

1. Création du fichier d'environnement (secret) .ENV :

2. Création du fichier .htaccess

3. Création du dossier "/public"
4. Ajout d'un nouveau fichier .htaccess (dans le dossier /public):

5. Création du repo sur GITHUB

6. GITBASH :
    // GIT CLONE https://github.com/Guigui5122/garagephp.git garage
puis dans le répertoire du projet : 
    // GIT INIT (créé un dossier .git dans le projet) // en local
    // git config user.name "mon user name github" 
    // git config user.email "mon email"

7. Création du fichier .gitignore à la racine du projet (voir fichier)
    -> Empêche de transmettre sur Git les fichiers sensibles .env / logs / dist / docker / bdd / tests

8. GITBASH : 
// git add . (envoi tous les fichiers)
// git commit -m "Commit initial" (1er commit)
// git remote add origin https://github.com/guigui5122/garagephp.git
// git branch -M main
// git push -u origin main

9. Création d'un dossier (racine) .github
    -> puis sous dossier : Workflows
      ->   puis un fichier main.yml

      -> git checkout -b dev (création de la branche DEV)
      Dans le fichier main.yml, on configure le CI (Continous Integration)

      -> Puis on push sur git pour avoir le suivi Github Actions, vu du workflows !

10. Création du fichier composer.json
11. Composer install (création du composer.lock, dossier VENDOR, etc...)
12. Création du dossier CONFIG (manuelle - voir commentaire) || ou version vlucas/
        puis du fichier config.php
 -> 

13. Création du fichier Database.php (dans /Config)
    sprintf construit le DataSourceName ($dsn)

14. Créer le dossier SQL / Fichier garagephp.sql
15. Créer le script SQL pour la bdd, les tables
    > on insère un 'user' qui sera 'Admin'

16. Créer un dossier SRC/Models/ et les 
fichiers :
- BaseModel.php
- Users.php

17. Création du fichier .env.exemple

18. Création du fichier : phpunit.xml.dist

19. Création des fichiers Docker-compose.yml, et Dockerfile

20. Commande pour créer les conteneurs : "docker-compose up -d --build"

21. Installer les dépendances "composer require --dev phpunit/phpunit"

22. Création du fichier index.php

23. Conception des Models (dossier src/Models)

    - Création de la class BaseModel.php
    - Création de la class car.php
    - Création de la class user.php   
    

24. Conception des Controllers (dossier src/Controllers)
    - Création de la class BaseController.php
    - Création de la class HomeController.php
    - Création de la class AuthController.php
    - Création de la class CarController.php
 
25. Dossier Security / Validator.php
@todo : 
tokenmanager
tests

✅Fichier court et séparer les responsabilités