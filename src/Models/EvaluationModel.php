<?php
namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Modèle gérant les interactions avec la table des évaluations (Evaluate).
 */
class EvaluationModel extends Model
{
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Enregistre une évaluation. Si l'utilisateur a déjà noté cette entreprise, 
     * la note existante est mise à jour (SFx 5).
     */
    public function saveEvaluation($data)
    {
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
     * Récupère la liste des avis pour une entreprise donnée avec les noms des auteurs (SFx 2).
     */
    public function getEvaluationsByCompany($id_company)
    {
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
     * Calcule la moyenne des notes reçues par une entreprise (SFx 2).
     * @return float|null Retourne la moyenne ou null si aucune note n'est présente.
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