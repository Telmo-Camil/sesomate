<?php
namespace App\Core;
use PDO;
use PDOException;
/**
 * This interface represents a database.
 */
class Database
{

    public function __construct()
    {
        // Ici, on pourrait initialiser une connexion à une base de données réelle
    }
    public function connect()
    {
        try {
           $pdo = new PDO('mysql:host=127.0.0.1;dbname=sesomate;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
        return $pdo;
    }
}