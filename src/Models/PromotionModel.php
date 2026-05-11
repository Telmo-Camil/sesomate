<?php
namespace App\Models;
use App\Core\Model;

class PromotionModel extends Model
{
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPromotionById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM Promotion
             WHERE ID_pilote = :id'
        );
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getOffersByPromotionId(int $promotionId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT o.*, po.ID_promotion, c.Name as CompanyName
             FROM JobOffer o
             JOIN Promotion_Offer po ON o.ID_offer = po.ID_offer
             JOIN Company c ON o.ID_company = c.ID_company
             WHERE po.ID_promotion = :promotionId'
        );
        $stmt->bindValue(':promotionId', $promotionId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}