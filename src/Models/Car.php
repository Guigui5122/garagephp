<?php
namespace App\Models;

use PDO;

// Modèle Car, représente une voiture en BDD

class Car extends BaseModel{


    protected string $table = 'cars';

    /**
     * Récupère toutes les voitures
     * @return array tableau de voitures
     */
    public function all(): array{
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        //Fetch_ASSOC est déjà défini par défaut dans notre classe DATABASE
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // méthode pour trouver une voiture avec son ID
    public  function find(int $car_id): ?array{
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE car_id =  :id");
        $stmt->execute([':id'=> $car_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
