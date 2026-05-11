<?php
namespace App\Models;

use App\Core\Model;

class StudentModel extends Model{

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function searchStudent(string $search){
        $search = trim($search);

        if (empty($search)) {
            return [];
        }

        $searchParam = '%' . $search . '%';

        $sql = "
            SELECT
                u.ID_user,
                u.Email,
                r.Status as status,  -- On récupère le nom du rôle pour l'affichage
                p.Name,
                p.Lastname,
                p.Phone_number
            FROM User_ u
            INNER JOIN Profil p ON u.ID_profil = p.ID_profil
            INNER JOIN Role r ON u.ID_role = r.ID_role  -- Jointure avec la table Role
            WHERE 
                r.ID_role = 1  -- Filtre pour ne prendre que les étudiants
                AND (
                    p.Name LIKE :search1
                    OR p.Lastname LIKE :search2
                    OR u.Email LIKE :search3
                )
            ORDER BY p.Lastname ASC, p.Name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':search1', $searchParam, \PDO::PARAM_STR);
        $stmt->bindParam(':search2', $searchParam, \PDO::PARAM_STR);
        $stmt->bindParam(':search3', $searchParam, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createStudent(string $email, string $password, string $name, string $lastname, string $phone, ?string $photoPath = null) {
        try {
            // 1. Démarrer une transaction pour garantir l'intégrité des données
            $this->pdo->beginTransaction();

            // 2. Insérer d'abord dans la table Profil pour obtenir l'ID_profil
            $stmtProfil = $this->pdo->prepare("INSERT INTO Profil (Name, Lastname, Phone_number, Photo_path) VALUES (:name, :lastname, :phone, :photoPath)");
            $stmtProfil->execute([
                'name'     => $name,
                'lastname' => $lastname,
                'phone'    => $phone,
                'photoPath' => $photoPath
            ]);

            // Récupérer l'ID du profil qui vient d'être créé
            $idProfil = $this->pdo->lastInsertId();

            // 3. Insérer dans la table User_
            // On utilise ID_role = 1 pour "etudiant" comme vu précédemment
            $stmtUser = $this->pdo->prepare("INSERT INTO User_ (Email, Password, ID_profil, ID_role) VALUES (:email, :password, :id_profil, :id_role)");
            
            // Hachage du mot de passe pour la sécurité
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmtUser->execute([
                'email'     => $email,
                'password'  => $hashedPassword,
                'id_profil' => $idProfil,
                'id_role'   => 1 // ID correspondant à 'etudiant' dans ta table Role
            ]);

            // 4. Valider la transaction
            $this->pdo->commit();

        } catch (\Exception $e) {
            // En cas d'erreur (email déjà existant, etc.), on annule tout
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Log l'erreur ou la propager
            throw $e;
        }
    }


    public function updateStudent(int $idUser, string $email, string $name, string $lastname, string $phone, ?string $newPassword = null, ?string $photoPath = null) {
        try {
            $this->pdo->beginTransaction();

            // 1. Récupérer l'ID_profil associé à cet utilisateur
            $stmtId = $this->pdo->prepare("SELECT ID_profil FROM User_ WHERE ID_user = :id");
            $stmtId->execute(['id' => $idUser]);
            $idProfil = $stmtId->fetchColumn();

            if (!$idProfil) {
                throw new Exception("Utilisateur non trouvé.");
            }

            // 2. Mettre à jour la table Profil
            $sqlProfil = "UPDATE Profil SET Name = :name, Lastname = :lastname, Phone_number = :phone, Photo_path = :photoPath WHERE ID_profil = :id_profil";
            $this->pdo->prepare($sqlProfil)->execute([
                'name'      => $name,
                'lastname'  => $lastname,
                'phone'     => $phone,
                'photoPath' => $photoPath,
                'id_profil' => $idProfil
            ]);

            // 3. Mettre à jour la table User_ (Email)
            $sqlUser = "UPDATE User_ SET Email = :email , ID_role =1";
            $paramsUser = ['email' => $email, 'id' => $idUser];

            // 4. Si un nouveau mot de passe est fourni, on l'ajoute à la requête
            if (!empty($newPassword)) {
                $sqlUser .= ", Password = :password";
                $paramsUser['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            }

            $sqlUser .= " WHERE ID_user = :id";
            $this->pdo->prepare($sqlUser)->execute($paramsUser);

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function deleteStudent(int $idUser) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("SELECT Photo_path, Email from User_ JOIN Profil ON User_.ID_profil = Profil.ID_profil WHERE User_.ID_user = :id");
            $stmt->execute(['id' => $idUser]);
            $photoPath = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($photoPath) {
                unlink($photoPath['Photo_path']);// Supprimer la photo du serveur
                rmdir(__DIR__ . '/../../public/Photos/' . $photoPath['Email']);
            }
            // 1. Récupérer l'ID_profil avant de supprimer l'utilisateur
            $stmt = $this->pdo->prepare("SELECT ID_profil FROM User_ WHERE ID_user = :id");
            $stmt->execute(['id' => $idUser]);
            $idProfil = $stmt->fetchColumn();

            if ($idProfil) {
                $tables = ['Apply', 'Wishlist','Evaluate']; // ajoutez toutes les tables liées
                foreach ($tables as $table) {
                    $stmt = $this->pdo->prepare("DELETE FROM $table WHERE ID_user = :id");
                    $stmt->execute([':id' => $idUser]);
                }
                // 2. Supprimer l'utilisateur d'abord (table dépendante)
                $stmtUser = $this->pdo->prepare("DELETE FROM User_ WHERE ID_user = :id");
                $stmtUser->execute(['id' => $idUser]);

                // 3. Supprimer le profil (table parente)
                $stmtProfil = $this->pdo->prepare("DELETE FROM Profil WHERE ID_profil = :id_profil");
                $stmtProfil->execute(['id_profil' => $idProfil]);
            }

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function isStudent(int $idUser){
        $sql = "
            SELECT
                ID_role
            FROM User_
            WHERE 
                ID_user = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $idUser, \PDO::PARAM_INT);
        $stmt->execute();
        $result=$stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result && $result['ID_role'] == 1){
            return True;
        }
        return False;

    }
}