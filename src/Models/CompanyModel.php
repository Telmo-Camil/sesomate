<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class CompanyModel extends Model
{

    /**
     * Constructeur : Reçoit l'instance PDO pour les requêtes
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Insère une nouvelle entreprise dans la table 'Company'
     */
    public function create($data)
    {
        $sql = "INSERT INTO Company (Name, Email, Phone_Number, Description) 
                VALUES (:name, :email, :phone, :description)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description']
        ]);
    }

    /**
     * Récupère toutes les entreprises
     */
    public function getAll()
    {
        $sql = "SELECT * FROM Company ORDER BY ID_company DESC"; 
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une entreprise par son ID
     */
    public function delete($id)
    {
        $sql = "SELECT ID_offer FROM Job_offer WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $jobOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($jobOffers as $jobOffer) {
            $sql = "DELETE FROM Wishlist WHERE ID_offer = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $jobOffer['ID_offer']]);
        }
        
        $sql = "DELETE FROM Evaluate WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $sql = "DELETE FROM Job_offer WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $sql = "DELETE FROM Company WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Récupérer les informations de l'entreprise pour un identifiant spécifique.
     */
    public function getById($id) {
        $sql = "SELECT * FROM Company WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jours les informations d'entreprise
     */
    public function update($id, $data) {
        $sql = "UPDATE Company SET Name = :name, Email = :email, Phone_Number = :phone, Description = :description 
                WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description']
        ]);
    }
}