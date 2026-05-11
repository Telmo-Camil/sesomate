<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\JobOfferModel;
use App\Models\AccessModel;

class ManageOfferController extends Controller {

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    // Affiche le tableau avec toutes les offres
    public function index() {
        $model = new JobOfferModel($this->pdo);
        $offres = $model->getAllOffers(); // Utilisez votre méthode existante qui liste tout

        echo $this->twig->render('offers_list.html.twig', [
            'offres' => $offres
        ]);
    }

    // Affiche le formulaire (vide pour une création, pré-rempli pour une modification)
    public function form($id_offer = null) {
        $offer = null;
        
        // Si un ID est fourni, c'est une modification, on récupère les données
        if ($id_offer) {
            $model = new JobOfferModel($this->pdo);
            $offer = $model->getOfferById($id_offer);
        }

        // Il faudrait aussi récupérer la liste des entreprises pour le menu déroulant (<select>)
        $stmt = $this->pdo->query("SELECT * FROM Company");
        $companies = $stmt->fetchAll();

        echo $this->twig->render('offer_form.html.twig', [
            'offer' => $offer,
            'companies' => $companies
        ]);
    }

    // Traite les données du formulaire quand on clique sur "Enregistrer"
    public function save() {
        $model = new JobOfferModel($this->pdo);
        
        $data = [
            'Title' => $_POST['Title'],
            'Description' => $_POST['Description'],
            'Category' => $_POST['Category'],
            'Salary' => $_POST['Salary'],
            'Duration' => $_POST['Duration'],
            'Start_date' => $_POST['Start_date'],
            'ID_company' => $_POST['ID_company']
        ];

        if (!empty($_POST['ID_offer'])) {
            // Modification
            $model->updateOffer($_POST['ID_offer'], $data);
        } else {
            // Création
            $data['Create_date'] = date('Y-m-d'); // Date du jour
            $model->createOffer($data);
        }

        redirect('/admin/offer');
    }

    // Supprime l'offre
    public function delete($id_offer) {
        $model = new JobOfferModel($this->pdo);
        $model->deleteOffer($id_offer);
        
        redirect('/admin/offer');
    }
}