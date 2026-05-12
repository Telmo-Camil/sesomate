<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompanyModel;
use App\Models\AccessModel;

/**
 * Contrôleur gérant toutes les opérations liées aux entreprises.
 * Correspond aux fonctionnalités SFx 2, 3, 4 et 6.
 */
class CompanyController extends Controller {

    private $companyModel;

    /**
     * Constructeur : Initialise Twig, la connexion PDO et les modèles nécessaires.
     */
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->companyModel = new CompanyModel($this->pdo);
        $this->Model = new AccessModel($this->pdo);
    }

    /**
     * Affiche le menu principal de la gestion des entreprises.
     * URL: /admin/entreprises
     */
    public function index() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('admin-entreprises-menu.html.twig',['user'=> $user]);
    }

    /**
     * Affiche le formulaire pour créer une nouvelle entreprise (SFx 3).
     * URL: /admin/entreprises/create
     */
    public function create() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('creation-entreprise.html.twig',['user'=> $user]);
    }

    /**
     * Affiche la liste complète des entreprises et permet la recherche (SFx 2).
     * URL: /admin/entreprises/list
     */
    public function list() {
        $user = $this->Model->currentUser();
        $companies = $this->companyModel->getAll();
        echo $this->twig->render('list-entreprises.html.twig', [
            'companies' => $companies,
            'user' => $user
        ]);
    }

    /**
     * Traite les données du formulaire et enregistre l'entreprise en base de données (SFx 3).
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => $_POST['name'] ?? null,
                'email'       => $_POST['email'] ?? null,
                'phone'       => $_POST['phone'] ?? null,
                'description' => $_POST['description'] ?? null
            ];

            if ($data['name'] && $data['email']) {
                $success = $this->companyModel->create($data);

                if ($success) {
                    // Redirection vers la liste avec un message de succès
                    redirect('/admin/entreprises/list?success=created');
                } else {
                    echo "Erreur lors de l'insertion dans la base de données.";
                }
            } else {
                echo "Veuillez remplir tous les champs obligatoires.";
            }
        }
    }

    /**
     * Supprime une entreprise du système (SFx 6).
     * @param int $id Identifiant de l'entreprise à supprimer.
     */
    public function delete($id) {
        $success = $this->companyModel->delete($id);

        if ($success) {
            redirect('/admin/entreprises/list?success=deleted');
        } else {
            echo "Erreur lors de la suppression.";
        }
    }

    /**
     * Affiche le formulaire de modification pour une entreprise existante (SFx 4).
     */
    public function edit($id) {
        $company = $this->companyModel->getById($id);
        echo $this->twig->render('edit-entreprise.html.twig', ['company' => $company]);
    }

    /**
     * Met à jour les informations de l'entreprise après modification (SFx 4).
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'description' => $_POST['description']
            ];
            $this->companyModel->update($id, $data);
            redirect('/admin/entreprises/list?success=updated');
        }
    }

    /**
     * Affiche les évaluations et la moyenne d'une entreprise spécifique (SFx 2).
     */
    public function showEvaluations($id_company) {
        $company = $this->companyModel->getById($id_company);
        
        $evaluationModel = new \App\Models\EvaluationModel($this->pdo);
        $evaluations = $evaluationModel->getEvaluationsByCompany($id_company);
        $average = $evaluationModel->getAverageRate($id_company);

        echo $this->twig->render('view-evaluations.html.twig', [
            'company' => $company,
            'evaluations' => $evaluations,
            'average' => $average
        ]);
    }
}