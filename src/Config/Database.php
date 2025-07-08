<?php

namespace App\Config;

use PDO;
use PDOException;


/**
 * Gère la connexion à la base de données avec le Design Pattern : Singleton
 * ((A  FAIRE DANS TOUS LES PROJETS))
 */

class Database
{

    // Propriété statique privée pour stocker l'instance unique de PDO
    private static ?PDO $instance = null;

    // Le constructeur est privé pour empêcher la création d'objets via "new Database"
    private function __construct() {}
    // La méthode de clonage est privée pour empêcher de cloner l'instance
    private function __clone() {}

    public static function getInstance(): PDO
    {

        // si l'instance n'a pas été créée
        if (self::$instance === null) {

            // On construit le DSN (Data Source Name) avec les infos du fichier .env !
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", Config::get('DB_HOST'), Config::get('DB_PORT', '3306'), Config::get('DB_NAME'));

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // lance des exceptions en cas d'erreur SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // récupère les résultatssous forme de tableau associatif
            ];

            try {

                // On créer l'instance de PDO et on la stock
                self::$instance = new PDO($dsn, Config::get('DB_USER'), Config::get('DB_PASSWORD'), $options);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
