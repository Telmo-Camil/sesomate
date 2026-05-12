<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProfilModel;

/**
 * Contrôleur gérant le profil utilisateur.
 * Permet d'afficher et de mettre à jour les informations personnelles.
 */
class ProfilController extends Controller {
    protected $twig;
    protected $pdo;

    /**
     * Constructeur : Initialise le moteur de rendu Twig et la connexion à la base de données.
     */
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    /**
     * Affiche les informations complètes du profil de l'utilisateur connecté.
     * URL: /profil
     */
    public function showInfos() {
        // Initialisation de la session si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Vérification de l'authentification : redirige vers la connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION['user_id'])) { 
            redirect('/connexion'); 
        }

        $model = new ProfilModel($this->pdo); 
        // Récupération des données utilisateur via le modèle
        $user = $model->getUserFullInfo($_SESSION['user_id']);

        // Rendu de la vue avec les données de l'utilisateur
        echo $this->twig->render('profil-infos.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Traite la mise à jour des informations du profil à partir des données POST.
     */
    public function update() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Sécurité : empêche l'accès si l'ID utilisateur est manquant en session
        if (!isset($_SESSION['user_id'])) exit();

        $model = new ProfilModel($this->pdo);
        // Appel de la méthode de mise à jour dans le modèle avec les données du formulaire
        $model->updateProfil($_SESSION['user_id'], $_POST);

        // Redirection vers la page de profil après la modification
        redirect('/profil');
    }
}