<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\PiloteModel;
use App\Models\AccessModel;

class UserController extends Controller
{
    private $UserModel;
    private $StudentModel;
    private $PiloteModel;

    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->UserModel = new UserModel($this->pdo);
        $this->StudentModel = new StudentModel($this->pdo);
        $this->PiloteModel = new PiloteModel($this->pdo);
        $this->Model = new AccessModel($this->pdo);
    }

    public function createMenu()
    {
        $user = $this->Model->currentUser();
        echo $this->twig->render("inscription.html.twig", [
            'roles' => $this->UserModel->rolesList(),
            'user' => $user
        ]);
    }


    public function userCreate()
    {
        // Imaginons que les données viennent d'un formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['Role'] == 'Etudiant') {
                try {
                    $Path = $this->UserModel->storePhoto($_FILES['photo'], $_POST['email']);
                    $this->StudentModel->createStudent(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone'],
                        $Path
                    );
                    redirect('/admin/utilisateurs');
                } catch (\Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
            if ($_POST['Role'] == 'Pilote') {
                try {
                    $Path = $this->UserModel->storePhoto($_FILES['photo'], $_POST['email']);
                    $this->PiloteModel->createPilote(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone'],
                        $Path
                    );
                    redirect('/admin/utilisateurs');
                } catch (\Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
        }
        redirect('/admin/utilisateurs');
    }

    public function userModify($id)
    {
        $Access = $this->Model->currentUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->UserModel->searchUserByID($id);
            echo $this->twig->render('user-modify.html.twig', [
                'users' => $user,
                'user' => $Access,
                'roles' => $this->UserModel->rolesList()
            ]);
            exit;
        }
    }

    public function userModified($id)
    {
        // Imaginons que les données viennent d'un formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Path = null; // Initialisation : null si pas de photo uploadée
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $this->UserModel->deletePhoto($id);
                $Path = $this->UserModel->storePhoto($_FILES['photo'], $_POST['email']);
            }
            if ($_POST['Role'] == 'Etudiant') {
                try {
                    $this->StudentModel->updateStudent(
                        $id,
                        $_POST['email'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone'],
                        $_POST['password'],
                        $Path
                    );
                    redirect('/admin/utilisateurs');
                } catch (\Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
            if ($_POST['Role'] == 'Pilote') {
                try {
                    $this->PiloteModel->updatePilote(
                        $id,
                        $_POST['email'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone'],
                        $_POST['password'],
                        $Path
                    );
                    redirect('/admin/utilisateurs');
                } catch (\Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
        }
        redirect('/admin/utilisateurs');
    }

    public function userDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->StudentModel->isStudent($id)) {
                $this->StudentModel->deleteStudent($id);
            } elseif ($this->PiloteModel->isPilote($id)) {
                $this->PiloteModel->deletePilote($id);
            }
        }
        redirect('/admin/utilisateurs');
    }


}