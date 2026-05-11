<?php
namespace App\Controllers;
use App\Core\Controller;    
use App\Models\WishlistModel;
use App\Models\AccessModel; 

class WishlistController extends Controller {

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->Model = new WishlistModel($this->pdo);
    }

    public function index() {
        $access = new AccessModel($this->pdo);
        $user = $access->currentUser();

        // Redirection si non connecté
        if (!$user) {
            redirect('/connexion');
        }

        // On utilise la vraie clé 'id' découverte dans le débug
        $id_user = is_array($user) ? $user['id'] : $user->id; 
        
        $offres = $this->Model->getAllByUserId($id_user);

        echo $this->twig->render('wishlist.html.twig', [
            'offres' => $offres,
            'user' => $user,
            'page_title' => 'Ma Wish-list'
        ]);
    }

    public function add($id_offer) {
        $access = new AccessModel($this->pdo);
        $user = $access->currentUser();

        if ($user && $id_offer) {
            $id_user = is_array($user) ? $user['id'] : $user->id; 
            $this->Model->add($id_user, $id_offer);
        }
        
    }

    public function delete($id_offer) {
        $access = new AccessModel($this->pdo);
        $user = $access->currentUser();

        if ($user && $id_offer) {
            $id_user = is_array($user) ? $user['id'] : $user->id; 
            $this->Model->remove($id_user, $id_offer);
        }
        
        // On renvoie l'utilisateur sur la page d'où il vient
        $retour = $_SERVER['HTTP_REFERER'] ?? '/wishlist';
        redirect($retour);
        exit();
    }
}