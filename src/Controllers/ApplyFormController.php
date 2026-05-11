<?php
namespace App\Controllers;
use App\Models\CandidacyModel;
use App\Core\Controller;
use App\Models\JobOfferModel;
use App\Models\AccessModel;
class ApplyFormController extends Controller
{
    private $jobModel;
    private $access;
    public function __construct($twig, $pdo)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
        $this->Model = new CandidacyModel($pdo);
        $this->jobModel = new JobOfferModel($pdo);
        $this->access = new AccessModel($pdo);
    }

    public function storeCandidacy(int $offerId): void
    {

        $user = $this->access->currentUser();

        $userId = (int) $user['id'];

        $companyName = $this->jobModel->getOfferById($offerId)['CompanyName'];

        $comment = $_POST['comment'];
        $LM = $_FILES['LM'] ?? null;
        $CV = $_FILES['CV'] ?? null;
        if ($this->Model->createCandidacy($userId, $offerId, $comment, $LM, $CV, $companyName)) {
            $_SESSION['flash'] = 'Votre candidature a bien été envoyée !';
            redirect('/');
            exit;
        } else {
            $offre = $this->jobModel->getOfferById($offerId);
            $erreur = $this->Model->getErreur();
            if ($erreur === 1) {
                $erreurMessage = 'Vous avez déjà postulé à cette offre.';
            } else {
                $erreurMessage = 'Une erreur est survenue lors de la soumission de votre candidature.';
            }
            echo $this->twig->render('formulaire-postuler.html.twig', [
                'offre' => $offre,
                'user' => $user['pseudo'],
                'erreur' => $erreurMessage,
            ]);
        }

    }

    public function printApplyForm(int $offerId): void
    {

        $user = $this->access->currentUser();
        $offre = $this->jobModel->getOfferById($offerId);
        echo $this->twig->render('formulaire-postuler.html.twig', ['offre' => $offre, 'user' => $user]);

    }
}