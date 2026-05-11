<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class JobOfferModel extends Model{
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les offres avec l'ID de l'entreprise
     */
    public function getAllOffers() {
        $sql = "SELECT j.ID_offer, j.ID_company, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                ORDER BY j.ID_offer DESC"; 

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une offre par son ID (Modifié pour récupérer TOUTES les infos pour le formulaire)
     */
    public function getOfferById($id) {
        // On sélectionne j.* pour avoir la catégorie, les dates, etc.
        $sql = "SELECT j.*, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                WHERE j.ID_offer = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les meilleures offres basées sur les notes
     */
    public function getBestOffer($limit = 5){
        $sql = "SELECT j.ID_offer, j.ID_company, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName, AVG(e.Rate) as average_rating
                FROM Job_offer j
                JOIN Company c ON j.ID_company = c.ID_company
                JOIN Evaluate e ON c.ID_company = e.ID_company
                GROUP BY j.ID_offer, j.ID_company, j.Category, j.Title, j.Duration, j.Salary, c.Name 
                ORDER BY average_rating DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche et filtrage des offres
     */
    public function searchAndFilterOffers(string $keyword = '', string $category = '', string $minSalary = '', string $duration = '') {
        $sql = "SELECT j.ID_offer, j.ID_company, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                WHERE 1=1"; 
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (j.Title LIKE :keyword OR j.Description LIKE :keyword OR c.Name LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($category)) {
            $sql .= " AND j.Category = :category";
            $params[':category'] = $category;
        }

        if (!empty($minSalary)) {
            $sql .= " AND j.Salary >= :min_salary";
            $params[':min_salary'] = (float)$minSalary;
        }

        if (!empty($duration)) {
            $sql .= " AND j.Duration = :duration";
            $params[':duration'] = (int)$duration;
        }

        $sql .= " ORDER BY j.ID_offer DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // NOUVELLES MÉTHODES CRUD (Gestion des offres)
    // ==========================================

    /**
     * Crée une nouvelle offre
     */
    public function createOffer($data) {
        $sql = "INSERT INTO Job_offer (Title, Description, Category, Salary, Duration, Create_date, Start_date, ID_company) 
                VALUES (:Title, :Description, :Category, :Salary, :Duration, :Create_date, :Start_date, :ID_company)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Modifie une offre existante
     */
    public function updateOffer($id_offer, $data) {
        $data['ID_offer'] = $id_offer; // On s'assure d'ajouter l'ID dans le tableau pour le WHERE
        $sql = "UPDATE Job_offer 
                SET Title = :Title, Description = :Description, Category = :Category, 
                    Salary = :Salary, Duration = :Duration, Start_date = :Start_date, ID_company = :ID_company 
                WHERE ID_offer = :ID_offer";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Supprime une offre (et ses liens dans les autres tables)
     */
    public function deleteOffer($id_offer) {
        // 1. Supprimer l'offre des favoris pour éviter l'erreur de contrainte de clé étrangère
        $stmtWishlist = $this->pdo->prepare("DELETE FROM Wishlist WHERE ID_offer = :id");
        $stmtWishlist->execute(['id' => $id_offer]);

        // 2. Supprimer les candidatures liées à cette offre
        $stmtApply = $this->pdo->prepare("DELETE FROM Apply WHERE ID_offer = :id");
        $stmtApply->execute(['id' => $id_offer]);

        // 3. Supprimer l'offre elle-même
        $stmtOffer = $this->pdo->prepare("DELETE FROM Job_offer WHERE ID_offer = :id");
        return $stmtOffer->execute(['id' => $id_offer]);
    }
}