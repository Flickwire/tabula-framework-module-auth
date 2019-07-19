<?php
namespace Tabula\Modules\Auth\Pages;

use Tabula\Tabula;
use Tabula\Modules\Auth\Auth;
use Tabula\Modules\Auth\Models\Users;
use Tabula\Renderer\Page;

class Login {
    private $tabula;
    private $auth;
    private $userModel;

    public function __construct(Tabula $tabula, Auth $auth){
        $this->tabula = $tabula;
        $this->auth = $auth;
        $this->userModel = new Users($tabula);
    }

    public function render() {
        $request = $this->tabula->registry->getRequest();
        $session = $this->tabula->session;

        $page = new Page($this->tabula, 'modules/auth/login.html');
        $error = false;

        if ($request->getMethod() === 'POST') {
            $email = $request->get('email',true);
            $password = $request->get('password',true);
            //Check form filled
            if (is_null($email) || $email === '') {
                $error = true;
                $session->addError("Please provide an email address");
            }
            if (!$error && (is_null($password) || $password === '')) {
                $error = true;
                $session->addError("Please provide a password");
            }
            if (!$error) {
                //find user
                if($this->userModel->login($email, $password)){
                    header('Location: ' . $session->getAfterAuthUrl(), true, 303);
                    die();
                } else {
                    $error = true;
                    $session->addError("Email address or password incorrect");
                }
            }
        }

        //Show errors
        $errors = $session->getErrors();
        $page->set('errors',$errors);
        $page->set('errorState',$error ? 'error' : '');

        //Add semantic
        $semantic = $this->tabula->registry->getUriBase() . '/vendor/semantic/ui/dist/';
        $page->set('semanticJs', $semantic . 'semantic.min.js');
        $page->set('semanticCss', $semantic . 'semantic.min.css');

        $page->set('urlRegister', $this->tabula->registry->getUriBase().'/register');

        $page->render();
    }
}