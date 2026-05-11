<?php
namespace App\Models;
use App\Core\Model;

class CandidacyModel extends Model
{
    private $erreur;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->erreur = 0;
    }
    public function createCandidacy(int $userId, int $offerId, string $comment, $LM, $CV, $companyName): bool
    {

        $verif = $this->pdo->prepare(
            'SELECT COUNT(*) FROM Apply WHERE ID_user = :userId AND ID_offer = :offerId'
        );
        $verif->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $verif->bindValue(':offerId', $offerId, \PDO::PARAM_INT);
        $verif->execute();
        $count = $verif->fetchColumn();

        if ($count > 0) {
            $this->erreur = 1;
            return false; // L'utilisateur a déjà postulé à cette offre
        }
        $targetDir = __DIR__ . '/../../public/uploads/' . $_SESSION["user_pseudo"] . $companyName . '/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($LM['name']);
        $targetFileLM = $targetDir . $filename;

        $filename = uniqid() . '_' . basename($CV['name']);
        $targetFileCV = $targetDir . $filename;

        if (move_uploaded_file($LM['tmp_name'], $targetFileLM) && move_uploaded_file($CV['tmp_name'], $targetFileCV)) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO Apply (ID_user, ID_offer, Comment, ML, CV)
             VALUES (:userId, :offerId, :comment, :lm, :cv)'
            );
            $stmt->bindValue(':lm', $targetFileLM);
            $stmt->bindValue(':cv', $targetFileCV);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':offerId', $offerId);
            $stmt->bindValue(':comment', $comment);
            return $stmt->execute();
        }
        return false;
    }
    public function getErreur(): int
    {
        return $this->erreur;
    }
}
