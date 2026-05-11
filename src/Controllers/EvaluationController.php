<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\EvaluationModel;
use App\Models\CompanyModel;

class EvaluationController extends Controller
{
    private $evaluationModel;
    private $companyModel;

    /**
     * Constructeur : Initialise les modèles et Twig
     * Note : Si la classe parente Controller n'a pas de constructeur, 
     * on retire parent::__construct.
     */
    public function __construct($twig, $pdo)
    {
        // On initialise directement les propriétés si parent::__construct n'existe pas
        $this->twig = $twig;
        $this->pdo = $pdo;
        
        $this->evaluationModel = new EvaluationModel($pdo);
        $this->companyModel = new CompanyModel($pdo);
    }

    /**
     * Affiche le formulaire d'évaluation
     */
    public function create($id_company)
    {
        // Vérification de la session utilisateur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
     * Enregistre l'évaluation
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

        redirect('/'); // Retour à l'accueil après évaluation
        exit();
    }
}