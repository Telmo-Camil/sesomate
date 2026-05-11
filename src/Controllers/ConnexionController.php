<?php
namespace App\Controllers;
use App\Models\AccessModel;
use App\Core\Controller;
class ConnexionController extends Controller
{

    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->Model = new AccessModel($pdo);
    }
    public function printConnexion(): void
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['email'], $_POST['motDePasse'])
        ) {
            $sesouvenir = !empty($_POST['sesouvenir']);
            $resultat = $this->Model->connect(
                $_POST['email'],
                $_POST['motDePasse'],
                $sesouvenir
            );

            if ($resultat['succes']) {
                redirect('/');
                exit;
            }

            echo $this->twig->render('connexion.html.twig', [
                'erreur' => $resultat['message'],
            ]);
            return;
        }

        echo $this->twig->render('connexion.html.twig', []);
    }

    public function needConnexion(string $redirection = '/connexion'): void
    {
        if (!$this->Model->isConnect()) {
            redirect($redirection);
            exit;
        }
    }

    public function needAdmin(string $redirection = '/'): void
    {
        $this->needConnexion();
        if ($_SESSION['user_admin'] !== 3) {
            redirect($redirection);
            exit;
        }
    }
    public function deconnect(): void
    {
        $this->Model->deconnect();
        redirect('/');
        exit;
    }
    public function curentUser()
    {
        return $this->Model->curentUser();
    }

}