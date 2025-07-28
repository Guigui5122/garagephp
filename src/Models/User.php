<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;

class User extends BaseModel
{

    protected string $table = 'users';


    private ?int $user_id = null;
    private string $username;
    private string $email;
    private string $password;
    private string $role;

    // Getters (récupérer les données)
    public function getId(): ?int
    {
        return $this->user_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): string
    {
        return $this->role;
    }


    // Setters avec validation (on utilise des methodes qui 'set' les différentes propriétes, C'est plus Secure !!!)

    public function setUsername(string $username): self {

        if(empty(trim($username)) || strlen($username)>50){
            throw new InvalidArgumentException("Nom d'utilisateur invalide.");
        }

        $this->username = trim($username);
        return $this;
    }
    public function setEmail(string $email): self {

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException("E-mail invalide.");
        }

        $this->email = trim(strtolower($email));
        return $this;
    }
    public function setPassword(string $password): self{

        if(strlen($password) < 9){
            throw new InvalidArgumentException("Mot de passe trop court.");
        }

        $this->password = password_hash($password, PASSWORD_ARGON2ID);
        return $this;
    }
    public function setRole(string $role): self{

        if(!in_array($role, ['user', 'admin'])){
            throw new InvalidArgumentException("Rôle invalide.");
        }
        $this-> role = $role;
        return $this;
    }

// Méthodes 
/**
 * Sauvegarde de l'utilisateur en BDD
 *
 */

    public function save():bool{

        // Logique pour insert||update
        if($this->user_id === null){

            $sql = "INSERT INTO {this->table} (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $this->db->prepare($sql); //requête préparée : on cache les informations, on se prémuni des attaques SQL
            $params = [
                ':username' => $this->username,
                ':email' => $this->email,
                ':password' => $this->password, // ATTENTION ici le mot de passe est déjà hasher!!
                ':role' => $this->role ?? 'user' //On assigne par défaut le rôle : user
            ];

        }else{

            $sql = "UPDATE {$this->table} SET username = :username, email = :email, role = :role WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);

            // On lie les paramètres pour la mise à jour :
             $params = [
                ':username' => $this->username,
                ':email' => $this->email,
                ':role' => $this->role ?? 'user', //On assigne par défaut le rôle : user
                ':user_id' => $this->user_id // ATTENTION la condition WHERE est important
            ];
        }
        $result = $stmt->execute($params);

        if($this->user_id === null && $result){
            $this->user_id = (int)$this->db->lastInsertId(); 
        }
        return $result;
        
    }
    /**
     * Trouve un utilisateur par son email
     * @return static|null l'objet user trouvé ou null
     */

    public function findByEmail(string $email): static {

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email= :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null; 

    }
/**
 * Vérifie les identifiants de l'utilisateurs
 * @return static|null l'objet user si l'authentification réussi sinon null
 * 
 */
    public function authenticate(string $email, string $password): ?static {

        $user = $this->findByEmail($email);

        //On verifie que l'utilisateur existe et que le MDP fournit correspond au MDP hashé stocké
        if($user && password_verify($password, $user->password)){
            return $user;
        }
        return null;
    }

    /**
     * Cette méthode remplie les propriétés de l'objet pour insérer en BDD
     */
    private function hydrate(array $data): static{
        $this->user_id = (int)$data['user_id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->role = $data['role'];

        return $this;
        }

};


