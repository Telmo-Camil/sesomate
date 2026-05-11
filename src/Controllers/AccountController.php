<?php
namespace App\Controllers;
use App\Models\AccessModel;
use App\Core\Controller;
class AccountController extends Controller
{
    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->Model = new AccessModel($pdo); 
    }
    public function index()
    {
        if (!$this->Model->isConnect()) {
            redirect('/connexion');
        }
        $user = $this->Model->currentUser();

        echo $this->twig->render('espace-compte.html.twig', [
            'user' => $user,
        ]);
    }
}