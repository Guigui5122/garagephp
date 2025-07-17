<?php 
// Affiche les erreurs directement dans la page
init_set('display_errors', 1);
error_reporting(E_ALL); //pour afficher les erreurs sur notre page

// Inclure l'autoloader
require_once __DIR__ . '/vendor/autoload.php'; //fichier généré par Composer

// Import des classes
use App\Config\Config;
use App\Utils\Response;

// Démarrer une session ou reprendre la session existante
session_start();

// Charger les variables d'environnement
Config::load(); // appel de la méthode créer dans le fichier Config.php

// Définir des routes avec la bibliothèque FastRoute 
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){
    // ajouter les routes avec ADDROUTE() 3 paramètres

    $r->addRoute('GET', '/', [App\Controllers\HomeController::class, 'index']);
    $r->addRoute('GET', '/login', [App\Controllers\AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [App\Controllers\AuthController::class, 'login']);// se connecter
    $r->addRoute('POST', '/logout', [App\Controllers\AuthController::class, 'logout']); //se déconnecter
    $r->addRoute('GET', '/cars', [App\Controllers\CarController::class, 'index']); //afficher les voitures
});

// Traitement de la requête

// Récupérer la méthode HTTP (GET / POST/ PUT/ PATCH) et l'URI (/login, /car/1, etc...)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Dispatcher FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = new Response();

// Analyser les résultats du dispatching
switch($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response->error("404 - Page non trouvée", 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->error("405 - Méthode non autorisée", 405);
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        try{
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $vars);
        }catch(\Exception $e){
            if(Config::get('APP_DEBUG') === 'true'){
                $response->error("Erreur 500 : " . $e->getMessage(). " dans " . $e->getFile() . ": " . $e->getLine(), 500);
            }else{
                (new \App\Utils\Logger())->log('ERROR', 'Erreur serveur :' . $e->getMessage());
                $response->error("Une erreur interne est survenue. ", 500);
            }
        }
        break;

    
}



