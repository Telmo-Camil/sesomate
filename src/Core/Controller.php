<?php
namespace App\Core;

/**
 * The base controller class for all controllers in the application.
 */
abstract class Controller {
    /**
     * The model associated with the controller.
     *
     * @var Model null
     */
    protected $Model = null;

    /**
     * The template engine used by the controller.
     *
     * @var  \Twig\Environment null
     */
    protected $twig = null;

    protected $pdo = null;
    
}