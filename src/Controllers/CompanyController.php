<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompanyModel;
use App\Models\AccessModel;

class CompanyController extends Controller {

    private $companyModel;

    /**
     * Constructeur : Initialise Twig, PDO et le modèle Company
     */
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->companyModel = new CompanyModel($this->pdo);
        $this->Model = new AccessModel($this->pdo);
    }

    /**
     * Menu principal de la gestion d'entreprise (écran de sélection de deux options)
     * URL: /admin/entreprises
     */
    public function index() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('admin-entreprises-menu.html.twig',['user'=> $user]);
    }

    /**
     * Formulaire de création d'entreprise à afficher
     * URL: /admin/entreprises/create
     */
    public function create() {
        $user = $this->Model->currentUser();
        echo $this->twig->render('creation-entreprise.html.twig',['user'=> $user]);
    }

    /**
     * Liste des entreprises
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
     * Enregistrer l'entreprise
     * URL: /admin/entreprises/store
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
                    // Redigier aux pages listes si réussir à créer
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
     * Supprimer l'entreprise
     * URL: /admin/entreprises/delete/:id
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
     * Modifier l'entreprise
     */
    public function edit($id) {
        $company = $this->companyModel->getById($id);
        echo $this->twig->render('edit-entreprise.html.twig', ['company' => $company]);
    }

    /**
     * Mettre à jour après la modification
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

    public function showEvaluations($id_company) {
        // On récupère les infos de l'entreprise (nom, etc.)
        $company = $this->companyModel->getById($id_company);
        
        // On utilise le modèle d'évaluation pour récupérer les avis
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