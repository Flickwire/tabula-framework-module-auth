<?php
namespace Tabula\Modules\Auth\Pages;

use Tabula\Tabula;
use Tabula\Modules\Auth\Auth;
use Tabula\Modules\Auth\Models\Users;

class Login {
    private $tabula;
    private $auth;
    private $userModel;

    public function __construct(Tabula $tabula, Auth $auth){
        $this->tabula = $tabula;
        $this->auth = $auth;
        $this->userModel = new Users($tabula->db);
    }

    public function render() {
        $request = $this->tabula->registry->getRequest();
        $session = $this->tabula->session;
        if ($request->getMethod() === 'POST') {
            $email = $request->get('email',true);
            $password = $request->get('password',true);
            $error = false;
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
                $user = $this->tabula->db->query("SELECT * FROM tb_users WHERE email = ?s",$email)->fetch();
                if (!$user) {
                    $error = true;
                    $session->addError("Email address or password incorrect");
                } else {
                    //check password
                    $verify = password_verify($password, $user['passwd']);
                    unset($password, $user['passwd']);

                    if ($verify) {
                        $session->setUserId($user['id']);
                        header('Location: ' . $session->getAfterAuthUrl(), true, 303);
                        die();
                    } else {
                        $error = true;
                        $session->addError("Email address or password incorrect");
                    }
                }
            }
        }
        $outMarkup = file_get_contents(__DIR__.DS.'html'.DS."login.html");

        //Show errors
        $errors = $session->getErrors();
        if ($errors !== []){
            $errortext = "";
            foreach ($errors as $error) {
                $errortext .= "<li>{$error}</li>";
            }
            $outMarkup = str_replace("_{ERRORS}_","<ul class=\"list\">{$errortext}</ul>",$outMarkup);
            $outMarkup = str_replace("_{ERROR_STATE}_","error ",$outMarkup);
        }
        $outMarkup = str_replace("_{ERRORS}_",'',$outMarkup);
        $outMarkup = str_replace("_{ERROR_STATE}_","",$outMarkup);

        $outMarkup = str_replace("_{SEMANTIC_PATH}_",$this->tabula->registry->getUriBase().'/vendor/semantic/ui/dist/',$outMarkup);
        $outMarkup = str_replace("_{REGISTER_URL}_",$this->tabula->registry->getUriBase().'/register',$outMarkup);
        echo($outMarkup);
    }
}