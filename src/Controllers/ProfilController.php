<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProfilModel;

class ProfilController extends Controller {
    protected $twig;
    protected $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function showInfos() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { redirect('/connexion'); }

        $model = new ProfilModel($this->pdo); 
        $user = $model->getUserFullInfo($_SESSION['user_id']);

        echo $this->twig->render('profil-infos.html.twig', [
            'user' => $user
        ]);
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) exit();

        $model = new ProfilModel($this->pdo);
        $model->updateProfil($_SESSION['user_id'], $_POST);

        redirect('/profil');
    }
}