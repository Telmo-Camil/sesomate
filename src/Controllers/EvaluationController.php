<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\EvaluationModel;
use App\Models\CompanyModel;

/**
 * Contrôleur gérant les évaluations données par les utilisateurs (SFx 5).
 */
class EvaluationController extends Controller
{
    private $evaluationModel;
    private $companyModel;

    /**
     * Initialise les services nécessaires à l'évaluation.
     */
    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;
        
        $this->evaluationModel = new EvaluationModel($pdo);
        $this->companyModel = new CompanyModel($pdo);
    }

    /**
     * Affiche l'interface permettant à l'étudiant de noter une entreprise (SFx 5).
     */
    public function create($id_company)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifie si l'utilisateur est bien connecté avant d'évaluer
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
        }

        $company = $this->companyModel->getById($id_company);

        if (!$company) {
            redirect('/');
            exit();
        }

        echo $this->twig->render('evaluation.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * Enregistre l'évaluation soumise dans la base de données (SFx 5).
     */
    public function store($id_company)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_user = $_SESSION['user_id'] ?? null;

        if (!$id_user) {
            redirect('/connexion');
        }

        $rate = $_POST['rate'] ?? null;
        $comment = $_POST['comment'] ?? '';

        if ($rate) {
            $data = [
                'id_user'    => $id_user,
                'id_company' => $id_company,
                'rate'       => (int)$rate,
                'comment'    => $comment
            ];

            $this->evaluationModel->saveEvaluation($data);
        }

        // Retour à la page d'accueil ou à la fiche entreprise après validation
        redirect('/'); 
        exit();
    }
}