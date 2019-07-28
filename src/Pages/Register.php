<?php
namespace Tabula\Modules\Auth\Pages;

use Tabula\Tabula;
use Tabula\Modules\Auth\Auth;
use Tabula\Modules\Auth\Models\Users;
use Tabula\Renderer\Page;

class Register {
    private $tabula;
    private $request;
    private $auth;
    private $userModel;

    public function __construct(Tabula $tabula, Auth $auth){
        $this->tabula = $tabula;
        $this->request = $this->tabula->registry->getRequest();
        $this->auth = $auth;
        $this->userModel = new Users($tabula);
    }

    public function render() {
        $session = $this->tabula->session;

        $page = new Page($this->tabula, 'modules/auth/register.html');
        $this->tabula->renderer->addScript('auth/register.js');

        $loginUrl = $this->tabula->registry->getUriBase().'/login';

        if ($this->request->getMethod() === 'POST'){
            $name = $this->request->get('name',true);
            $email = $this->request->get('email',true);
            $password = $this->request->get('password',true);
            $password2 = $this->request->get('password2',true);

            $passed = $this->userModel->validateUser($name,$email,$password,$password2);

            if($passed){
                $this->userModel->newUser($name,$email,$password);
                $session->addMessage('Your account has been created, you may log in.');
                header('Location: ' . $loginUrl, true, 303);
                die();
            }

            $page->set('email', $email);
            $page->set('name', $name);
        }

        //Show errors
        $errors = $session->getErrors();
        $page->set('errors',$errors);

        $page->set('title','Register');
        $page->set('urlLogin', $this->tabula->registry->getUriBase().'/login');

        //Add semantic
        $semantic = $this->tabula->registry->getUriBase() . '/vendor/semantic/ui/dist/';
        $page->set('semanticJs', $semantic . 'semantic.min.js');
        $page->set('semanticCss', $semantic . 'semantic.min.css');

        $page->render();
    }
}