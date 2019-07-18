<?php
namespace Tabula\Modules\Auth;

use Tabula\Tabula;
use Tabula\Router\Route;

/**
 * A secure route for the router
 * 
 * @author Skye
 */
class SecureRoute extends Route {
    private $tabula;

    public function __construct(Tabula $tabula, string $path, object $controller, string $method){
        parent::__construct($path, $controller, $method);
        $this->tabula = $tabula;
    }

    /**
     * Execute the method which handles this route
     * 
     * @author Skye
     */
    public function run(){
        if ($this->tabula->registry->getAuthHandler()->isLoggedIn) {
            return parent::run();
        }

        $this->tabula->session->setAfterAuthUrl($this->tabula->registry->getRequest()->getSelf());

        header('Location: ' . $this->tabula->registry->getUriBase() . '/login',true,303);
        die();
    }
}