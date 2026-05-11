<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class ProfilModel extends Model {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserFullInfo($id_user) {
        $sql = "SELECT U.ID_user, U.Email, U.Password, P.Name, P.Lastname, P.Phone_number 
                FROM User_ U 
                JOIN Profil P ON U.ID_profil = P.ID_profil 
                WHERE U.ID_user = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}