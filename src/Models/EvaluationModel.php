<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class EvaluationModel extends Model
{
    /**
     * Constructeur : Reçoit l'instance PDO pour les requêtes
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Insère ou met à jour une évaluation pour une entreprise
     * @param array $data Les données d'évaluation (id_user, id_company, rate, comment)
     * @return bool Succès ou échec de l'opération
     */
    public function saveEvaluation($data)
    {
        // On utilise REPLACE INTO ou une vérification pour éviter les doublons 
        // si un utilisateur a déjà évalué la même entreprise
        $sql = "INSERT INTO Evaluate (ID_user, ID_company, Rate, Comment) 
                VALUES (:id_user, :id_company, :rate, :comment)
                ON DUPLICATE KEY UPDATE Rate = :rate_upd, Comment = :comment_upd";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id_user'    => $data['id_user'],
            'id_company' => $data['id_company'],
            'rate'       => $data['rate'],
            'comment'    => $data['comment'],
            'rate_upd'   => $data['rate'],
            'comment_upd' => $data['comment'],
        ]);
    }

    /**
     * Récupère toutes les évaluations d'une entreprise spécifique
     * @param int $id_company L'identifiant de l'entreprise
     * @return array Liste des commentaires et notes
     */
    public function getEvaluationsByCompany($id_company)
    {
        // On joint Evaluate avec User_ puis avec Profil pour avoir le nom complet
        $sql = "SELECT E.*, P.Name, P.Lastname, U.Email 
                FROM Evaluate E
                JOIN User_ U ON E.ID_user = U.ID_user
                JOIN Profil P ON U.ID_profil = P.ID_profil
                WHERE E.ID_company = :id_company
                ORDER BY E.ID_user DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule la note moyenne d'une entreprise
     * @param int $id_company L'identifiant de l'entreprise
     * @return float|null La moyenne des notes
     */
    public function getAverageRate($id_company)
    {
        $sql = "SELECT AVG(Rate) as average FROM Evaluate WHERE ID_company = :id_company";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (float)$result['average'] : null;
    }
}