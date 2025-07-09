1. Création du fichier d'environnement (secret) .ENV :

#Base de données
DB_CONNECTION = mysql
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = garage_db
DB_USER = root
DB_PASSWORD = 

#Application
APP_ENV = developpement
APP_DEBUG = true
APP_KEY = #ici notre clé secrète
APP_URL = http://localhost:8080

#Sécurité
JWT_SECRET = # ici notre key secret #Json Web Token
CSRF_TOKEN_EXPIRE = 3600
SESSION_LIFETIME = 7200

#Email
MAIL_MAILER = smtp
MAIL_HOST = smtp.gmail.com
MAIL_PORT = 587
MAIL_USERNAME = mon adresse mail 
MAIL_PASSWORD = mon mot de passe
MAIL_ENCRYPTION = null
MAIL_FROM_NAME = "${APP_NAME : Mon Garage}" // si le client envoi un mail c'est l'app qui gère


#LOGS
LOG_LEVEL = debug 
LOG_FILE = storage/logs/app.log # Là ou sera notre fichier de logs


2. Création du fichier .htaccess

<<!-- Sécurité Apache : Masquer les informations du serveur et de la signature-->
ServeurTokens Prod
ServeurSignature Off

<!-- Redirection vers public/-->
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/ [NC] <!--ignorer la casse-->
    RewriteCond %{REQUEST_URI} !^index.php [NC] <!--evite les boucles si index.php est deja ciblé-->
    RewriteRule ^(.*)$ /public/$1 [L]
</IfModule>

<!-- Sécurité des fichiers sensibles -->
<FilesMatch "\.(env|log|sql|md|json|lock|yml|yaml|ini)$">
    Require all denied <!--Apache 2.4+ équivalent de Order allow,deny Allow from all-->
</FilesMatch>

<!-- Protection des répertoires -->
<DirectoryMatch "(config|src|tests|storage|vendor|bootstrap|resources)">
        Require all denied <!--Apache 2.4+ équivalent de Order allow,deny Allow from all-->
</DirectoryMatch>

<!-- Headers de sécurité -->
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    # Header always set Strict-Transport-Security "max-age = 31536000; includeSubDomains"
    <!--uniquement en HTTPS-->
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'"
</IfModule>


3. Création du dossier "/public"
4. Ajout d'un nouveau fichier .htaccess (dans le dossier /public):
<IfModule mod_rewrite.c>

    RewriteEngine On


    <!-- Redirection des URLs propres -->
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>

<!-- Sécurité : Contrôle d'accès aux fichiers PHP
Par défaut, Apache autorise l'accès aux fichiers -->
<Files "*.php">
    Require all granted <!--Apache 2.4+ équivalent de Order allow,deny Allow from all-->
</Files>

<!-- Cache statique via Expires Headers
 Améliore les performances en indiquant aux navigateurs de cacher les ressources statiques.-->
 
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 1 month" # Règle par défaut pour les autres types de fichiers
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year" # Ajout de GIF
    ExpiresByType image/webp "access plus 1 year" # Ajout de WebP
    ExpiresByType application/font-woff "access plus 1 year" # Ajout de polices
    ExpiresByType application/font-woff2 "access plus 1 year"
    ExpiresByType application/x-font-ttf "access plus 1 year"
    ExpiresByType font/opentype "access plus 1 year"
</IfModule>

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

