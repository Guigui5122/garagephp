<?php
namespace App\Models;
use App\Config\Database;
use PDO;

/**
 * Cette classe 'abstraite' sert a créer des fonctionnalités communes au autre modele
 * Elle ne pourra pas être instancier, mais sera Hérité par d'autres classes
 * 
 */

 abstract class BaseModel{

    /**
     * @var PDO l'instance de connection à la base de données
     */
    protected PDO $db;

    /**
     * @var string le nom de la table associée au modèle
     */
    protected string $table;

    public function __construct(?PDO $db = null){
        $this->db= $db ?? Database::getInstance();
    }
 };
 