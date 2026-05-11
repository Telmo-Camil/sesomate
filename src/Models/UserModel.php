<?php
namespace App\Models;

use App\Core\Model;


class UserModel extends Model
{

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function rolesList()
    {
        $sql = "SELECT Status FROM Role";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchUserByID(string $id)
    {
        $sql = "
            SELECT
                u.ID_user,
                u.Email,
                r.Status as status,
                p.Name,
                p.Lastname,
                p.Phone_number,
                p.Photo_path
            FROM User_ u
            INNER JOIN Profil p ON u.ID_profil = p.ID_profil
            INNER JOIN Role r ON u.ID_role = r.ID_role  -- Jointure avec la table Role
            WHERE 
                u.ID_user = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function storePhoto($file, $userId): ?string
    {
        $targetDir = __DIR__ . '/../../public/Photos/' . $userId . '/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile; // Retourne le chemin relatif pour la base de données
        }
        return NULL;
    }
    public function deletePhoto($id): void
    {
        $stmt = $this->pdo->prepare("SELECT Photo_path, Email from User_ JOIN Profil ON User_.ID_profil = Profil.ID_profil WHERE User_.ID_user = :id");
        $stmt->execute(['id' => $id]);
        $photoPath = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($photoPath) {
            unlink($photoPath['Photo_path']);// Supprimer la photo du serveur
            rmdir(__DIR__ . '/../../public/Photos/' . $photoPath['Email']);
        }
    }
}
