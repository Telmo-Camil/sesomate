<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class WishlistModel extends Model {
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllByUserId($id_user) {
        $sql = "SELECT o.*, c.Name as CompanyName FROM Job_offer o 
                JOIN Wishlist w ON o.ID_offer = w.ID_offer JOIN Company c ON o.ID_company = c.ID_company
                WHERE w.ID_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($id_user, $id_offer) {
        $sql = "INSERT IGNORE INTO Wishlist (ID_user, ID_offer) VALUES (:id_user, :id_offer)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_user' => $id_user, 
            'id_offer' => $id_offer
        ]);
    }

    public function remove($id_user, $id_offer) {
        $sql = "DELETE FROM Wishlist WHERE ID_user = :id_user AND ID_offer = :id_offer";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_user' => $id_user, 
            'id_offer' => $id_offer
        ]);
    }

    public function getUserWishlistIds($id_user) {
        $sql = "SELECT ID_offer FROM Wishlist WHERE ID_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        return array_map('intval', $ids);
    }
}