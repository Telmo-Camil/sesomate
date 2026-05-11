<?php
namespace App\Controllers;

use App\Models\JobOfferModel;
use App\Models\AccessModel;
use App\Models\WishlistModel; 

class HomeController
{
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $access = new AccessModel($this->pdo);
        $user = $access->currentUser(); 

        $jobOfferModel = new JobOfferModel($this->pdo);

        $offres = $jobOfferModel->getBestOffer();
        $allCategory = array_column($offres, 'Category');
        $UniqueCategory = array_unique($allCategory);
        
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        
       
        $wishlistIds = [];
        if ($user) {
           
            $userId = is_array($user) ? ($user['id'] ?? null) : ($user->id ?? null);
            
            if ($userId) {
                $wishlistModel = new WishlistModel($this->pdo);
                $wishlistIds = $wishlistModel->getUserWishlistIds($userId);
            }
        }

        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres,
            'category'      => $UniqueCategory,
            'user'          => $user,
            'flash'         => $flash,
            'wishlist_ids'  => $wishlistIds,
        ]);
    }
}