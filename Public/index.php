<?php

require_once '../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ConnexionController;
use App\Controllers\SearchController;
use App\Controllers\UserController;
use App\Controllers\AccountController;
use App\Controllers\ApplyFormController;
use App\Controllers\WishlistController;
use App\Controllers\OfferController;
use App\Controllers\CompanyController;
use App\Controllers\PromotionController;
use App\Core\Router;
use App\Core\Database;

// ─── Détection automatique du chemin de base (Serveur WAMP) ──────────────────
// Permet au routage de fonctionner même si le projet est dans un sous-dossier
// Ex: localhost/sesomate/public  →  BASE_PATH = '/sesomate/public'
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_PATH', $scriptDir);
define('BASE_URL',
    ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH
);

/**
 * Redirige vers une route de l'application (ex: '/', '/connexion').
 * Préfixe automatiquement BASE_URL pour WAMP et la production.
 */
function redirect(string $path): void
{
    // Si c'est déjà une URL complète (ex: HTTP_REFERER), on l'utilise directement
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . BASE_URL . $path);
    }
    exit;
}

// ─── Tampon de sortie : corrige les chemins absolus dans le HTML rendu ────────
// Remplace href="/...", action="/...", src="/..." par le bon chemin avec BASE_PATH
if (BASE_PATH !== '') {
    ob_start(function (string $output): string {
        return preg_replace(
            '/\b(href|action|src)="(\/(?!\/))/i',
            '$1="' . BASE_PATH . '/',
            $output
        );
    });
}

// ─── Extraction de la route depuis REQUEST_URI ────────────────────────────────
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routeUrl   = ltrim(substr($requestUri, strlen(BASE_PATH)), '/');

// ─── Initialisation Twig ──────────────────────────────────────────────────────
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig   = new \Twig\Environment($loader);
$twig->addGlobal('base_path', BASE_PATH);

// ─── Base de données ──────────────────────────────────────────────────────────
$db  = new Database();
$pdo = $db->connect();

$ConnexionController = new ConnexionController($twig, $pdo);

$router = new Router($routeUrl);

// ─── Routes ───────────────────────────────────────────────────────────────────

$router->get('/', function () use ($twig, $pdo) {
    (new HomeController($twig, $pdo))->index();
});
$router->get('/admin/Promotions/:id', function ($id) use ($twig, $pdo) {
    (new PromotionController($twig, $pdo))->index($id);
});
$router->get('/offer', function () use ($twig, $pdo) {
    (new OfferController($twig, $pdo))->index();
});
$router->get('/connexion', function () use ($ConnexionController) {
    $ConnexionController->printConnexion();
});
$router->post('/connexion', function () use ($ConnexionController) {
    $ConnexionController->printConnexion();
});
$router->get('/deconnexion', function () use ($ConnexionController) {
    $ConnexionController->deconnect();
    redirect('/');
});
$router->get('/postuler/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needConnexion();
    (new ApplyFormController($twig, $pdo))->printApplyForm($id);
});
$router->post('/postuler/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needConnexion();
    (new ApplyFormController($twig, $pdo))->storeCandidacy($id);
});
$router->get('/wishlist', function () use ($twig, $pdo) {
    (new WishlistController($twig, $pdo))->index();
});
$router->get('/wishlist/add/:id', function ($id) use ($twig, $pdo) {
    (new WishlistController($twig, $pdo))->add($id);
});
$router->get('/wishlist/delete/:id', function ($id) use ($twig, $pdo) {
    (new WishlistController($twig, $pdo))->delete($id);
});
$router->get('/espace-compte', function () use ($twig, $pdo) {
    (new AccountController($twig, $pdo))->index();
});
$router->get('/admin/utilisateurs', function () use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new SearchController($twig, $pdo))->searchUser();
});
$router->post('/admin/utilisateurs/results', function () use ($twig, $pdo) {
    (new SearchController($twig, $pdo))->resultUser();
});
$router->post('/admin/utilisateurs/delete/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new UserController($twig, $pdo))->userDelete($id);
});
$router->post('/admin/utlisateur/modify/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new UserController($twig, $pdo))->userModify($id);
});
$router->post('/admin/utlisateur/modified/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new UserController($twig, $pdo))->userModified($id);
});
$router->get('/admin/utilisateurs/create', function () use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new UserController($twig, $pdo))->createMenu();
});
$router->post('/inscription', function () use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new UserController($twig, $pdo))->userCreate();
});
$router->get('/utilisateur/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needAdmin();
    (new SearchController($twig, $pdo))->showUser($id);
});
$router->get('/admin/entreprises', function () use ($twig, $pdo) {
    (new CompanyController($twig, $pdo))->index();
});
$router->get('/admin/entreprises/create', function () use ($twig, $pdo) {
    (new CompanyController($twig, $pdo))->create();
});
$router->post('/admin/entreprises/store', function () use ($twig, $pdo) {
    (new CompanyController($twig, $pdo))->store();
});
$router->get('/admin/entreprises/list', function () use ($twig, $pdo) {
    (new CompanyController($twig, $pdo))->list();
});
$router->get('/admin/entreprises/delete/:id', function ($id) use ($twig, $pdo) {
    (new CompanyController($twig, $pdo))->delete($id);
});
$router->get('/admin/entreprises/edit/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\CompanyController($twig, $pdo))->edit($id);
});
$router->post('/admin/entreprises/update/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\CompanyController($twig, $pdo))->update($id);
});
$router->get('/mentions', function () use ($twig) {
    echo $twig->render('mentions-legales.html.twig');
});
$router->get('/evaluate/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\EvaluationController($twig, $pdo))->create($id);
});
$router->post('/evaluate/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\EvaluationController($twig, $pdo))->store($id);
});
$router->get('/admin/entreprises/evaluations/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\CompanyController($twig, $pdo))->showEvaluations($id);
});
$router->get('/profil/infos', function () use ($twig, $pdo) {
    (new \App\Controllers\ProfilController($twig, $pdo))->showInfos();
});
$router->get('/admin/offer', function () use ($twig, $pdo) {
    (new \App\Controllers\ManageOfferController($twig, $pdo))->index();
});
$router->get('/admin/offer/create', function () use ($twig, $pdo) {
    (new \App\Controllers\ManageOfferController($twig, $pdo))->form();
});
$router->get('/admin/offer/edit/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\ManageOfferController($twig, $pdo))->form($id);
});
$router->post('/admin/offer/save', function () use ($twig, $pdo) {
    (new \App\Controllers\ManageOfferController($twig, $pdo))->save();
});
$router->get('/admin/offer/delete/:id', function ($id) use ($twig, $pdo) {
    (new \App\Controllers\ManageOfferController($twig, $pdo))->delete($id);
});

$router->run();