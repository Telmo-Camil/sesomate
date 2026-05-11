<?php

namespace App\Controllers;
use App\Models\AccessModel;
use App\Models\PromotionModel;
use App\Core\Controller;

class PromotionController extends Controller
{
    private $promotionModel;
    public function __construct($twig, $pdo)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
        $this->Model = new AccessModel($pdo);
        $this->promotionModel = new PromotionModel($pdo);
    }

    public function index(int $id): void
    {


    }
}