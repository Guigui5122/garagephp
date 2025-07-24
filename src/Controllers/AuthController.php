<?php

namespace App\Controllers;

use App\Models\User;
use App\Security\Validator;
use App\Security\TokenManager;
use App\Utils\Logger;

/**
 * Cette classe gère les actions liées à l'authentification et à l'inscription des utilisateurs
 * 
 */

class AuthController extends BaseController{

    // Propriétés (ou attribut = importe les classes que nous allons instancier)
    private User $userModel;
    private TokenManager $tokenManager;
    private Logger $logger;

    // Constructeur 

    /* Le constructeur est appellé à chaque création d'un objet AuthController (ou nouvel instance),
    on en profite pour instancier les modeles dont on aura besoin
    */

    public function __construct(){
        // appel de BaseController qui se charge des responses et du validator
        parent::__construct();
        $this->userModel = new User();
        $this->tokenManager = new TokenManager();
        $this->logger = new Logger();
    }

    // Méthodes
    /**
     * Méthode qui affiche la page avec le formulaire de connexion
     */

    public function showLogin(): void {

        $this->render('auth/loggin', [
            'title' => 'Connexion',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    /**
     * Gère le Login utilisateur
     */

    public function login():void {

        // On s'assure que la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){

            // si NON, on redirige vers la page Login
            $this->response->redirect('/login');
        }

        $data = $this->getPostData();

        // Validation du jeton CSRF
        if(!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')){
            $this->response->error('Token de sécurité invalide.', 403);
        }
        // Appelle de la fonction authenticate() qui est définit dans Models\User qui gère la logique d'authentification
        $user = $this->userModel->authenticate($data['email'], $data['password']);

        if($user){

            // Si l'authentification réussie, on stocke les informations en session
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['user_username'] = $user->getUsername();

            // Si ok Redirection vers le tableau de bord
            $this->response->redirect('/cars');
        }else{

            // Si l'authentification échoue, on réaffiche le formulaire avec un message d'erreur
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Email ou Mot de passe incorrect.',
                'old' => ['email' => $data['email']], // renvoyer l'email qui a été saisie à l'utilisateur (pour vérifier)
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
        }
    }
    /**
     * Gestion de l'affichage du formulaire d'inscription
     * 
     */

    public function showRegister():void{
        
        $this->render('auth/register', [
            'title' => 'Inscription',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
        
    }   
    /**
     * Traitements des données : soumissions du formulaire d'inscription
     */
    
    public function register(): void{

        // On vérifie que la méthode est bien POST, sinon on redirige vers 'Register'
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){

            $this->response->redirect('/register');
        }

        $data = $this->getPostData();

        // Validation du jeton CSRF
        if(!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')){
            $this->response->error('Token de sécurité invalide.', 403);
        }

        // Validation des données du formulaire (avec les fonctions validator(), validate())
        $errors = $this->validator->validate($data, [
            'userame' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:9',
            'password_confirm' => 'required|same:password'
        ]);
        // Si le tableau d'erreur n'est pas vide :
        if(!empty($errors)){
            $this->render('auth/register', [

                'title' => 'Inscription',
                'errors' => $errors,
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
        // Vérification de l'email (voir s'il n'existe pas en base de données)
        if($this->userModel->findByEmail($data['email'])){
            $this->render('auth/register', [

                'title' => 'Inscription',
                // On affiche une erreur au champ email pour l'afficher
                'errors' => ['email' => ['Cette adresse email est déjà utilisée.']],
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        /**
         * Si tout est correct alors on crée un nouvel utilisateur
         */
        try{

            // On instancie un nouvel utilisateur
            $newUser = new User();

            // On utilise les setters pour assigner les valeurs (inclut la validation et le hashage du MDP)
            $newUser->setUsername($data['username'])
                    ->setEmail($data['email'])
                    ->setPassword($data['password'])
                    ->setRole($data['user']); // role par défaut
            
            // On sauvegarde en BDD
            if($newUser->save()){

                // Si la création à réussie, on connecte automatiquement l'utilisateur
                $_SESSION['user_id'] = $newUser->getId();
                $_SESSION['user_username'] = $newUser->getUsername();
                $_SESSION['user_role'] = $newUser->getRole();
                $this->response->redirect('/cars');
            }else{

                // Si la sauvegarde échoue
                throw new \Exception("La création du compte à échouée !");
            }

        }catch(\Exception $e){

            $this->render('auth/register', [

                'title' => 'Inscription',
                'errors' => "Erreur : " . $e->getMessage(), // Erreur générale
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()

            ]);
        }

    }
    /**
     * Méthode de déconnexion avec destruction de la session 
     * 
     */

    public function logout():void{

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){

        $this->response->redirect('/');
        
        }
        /**
         * Détruit toutes les données de la session actuelle
         */
        session_destroy();

        // Redirige vers la page de connexion
        $this->response->redirect('/login');

    }
}

