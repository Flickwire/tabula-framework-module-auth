<?php
namespace Tabula\Modules\Auth\Panes;

use Tabula\Modules\Auth\Models\Users;

if (interface_exists("\Tabula\Modules\Admin\AdminPane")){
    class UsersPane implements \Tabula\Modules\Admin\AdminPane {
        private $tabula;
        private $request;
        private $userModel;

        public function render(\Tabula\Tabula $tabula): string{
            $this->tabula = $tabula;
            $this->request = $this->tabula->registry->getRequest();
            $this->userModel = new Users($tabula->db);
            $action = $tabula->registry->getRequest()->get('action');
            switch($action){
                case 'create':
                    return $this->createUser();
                case 'edit':
                    //$outMarkup = file_get_contents(__DIR__.DS."html".DS."newUser.html");
                    //return $outMarkup;
                    return 'edit';
                case 'delete':
                    return $this->deleteUser();
                default:
                    return $this->listUsers();
            }
        }

        private function listUsers(): string{
            $outMarkup = \file_get_contents(__DIR__.DS."html".DS."listUsers.html");
            $users = $this->userModel->getUsers();
            foreach ($users as $user){
                $outMarkup = \str_replace("_{USERS}_","
                <tr>
                <td>{$user['displayname']}</td>
                <td>{$user['email']}</td>
                <td><a class=\"ui small negative right floated delete button\" href=\"{$this->request->getSelf(['action'=>'delete', 'id'=>$user['id']])}\">
                    Delete
                </a><a class=\"ui small right floated button\" href=\"{$this->request->getSelf(['action'=>'edit', 'id'=>$user['id']])}\">
                    Edit
                </a></td>
                </tr>
                _{USERS}_
                ",$outMarkup);
            }
            $outMarkup = \str_replace("_{USERS}_","",$outMarkup);
            $outMarkup = \str_replace("_{CREATE_URL}_",$this->request->getSelf(['action'=>'create']),$outMarkup);
            return $outMarkup;
        }

        private function createUser(): string{
            if ($this->request->getMethod() === 'POST'){
                $name = $this->request->get('name',true);
                $email = $this->request->get('email',true);
                $password = $this->request->get('password',true);
                $password2 = $this->request->get('password2',true);

                $passed = $this->validateUser($name,$email,$password,$password2);

                if($passed){
                    $this->userModel->newUser($name,$email,$password);
                    header('Location: ' . $this->request->getSelf([],['action'],true),true,303);
                    die();
                }
            }
            $outMarkup = \file_get_contents(__DIR__.DS."html".DS."newUser.html");
            $outMarkup = \str_replace("_{CANCEL_URL}_",$this->request->getSelf([],['action']),$outMarkup);
            return $outMarkup;
        }

        private function validateUser($name, $email, $password, $password2){
            $error = false;
            $session = $this->tabula->session;

            //password too short or absent
            if(\is_null($password) || \strlen($password) < 12){
                $session->addError('Your password must be at least 12 characters long');
                $error = true;
            } else {

                //password too short
                if(\strlen($password) > 128){
                    $session->addError('Your password may not be longer than 128 characters');
                    $error = true;
                }
    
                //password special character
                if(!\preg_match('/[!"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~\\\\]/',$password)){
                    $session->addError('Your password must contain a special character<br />(!"#$%&amp;\'()*+,-./:;&lt;=&gt;?@[]^_`{|}~\\)');
                    $error = true;
                }
    
                //password uppercase character
                if(!\preg_match('/[A-Z]/',$password)){
                    $session->addError('Your password must contain at least one uppercase character');
                    $error = true;
                }
    
                //password lowercase character
                if(!\preg_match('/[a-z]/',$password)){
                    $session->addError('Your password must contain at least one lowercase character');
                    $error = true;
                }
    
                //password number
                if(!\preg_match('/[0-9]/',$password)){
                    $session->addError('Your password must contain at least one number');
                    $error = true;
                }

                //Password doesn't match
                if($password !== $password2){
                    $session->addError('Your passwords must match');
                    $error = true;
                }

            }
    
            //email format
            if(!\preg_match('/[^@]+@[^\.]+\..+/',$email)){
                $session->addError('Please enter a valid email address');
                $error = true;
            }

            //password too short
            if(\is_null($name) || \strlen($name) < 1){
                $session->addError('Please enter your name as you would like it displayed');
                $error = true;
            }

            return !$error;
        }

        private function deleteUser(): string{
            if (!$this->request->has('id')){
                $this->tabula->session->addError('No user found to delete');
                header('Location: ' . $this->request->getSelf([],['action','id'],true),true,303);
                die();
            }
            $id = $this->request->get('id');
            if ($this->tabula->session->getUserId() === $id){
                $this->tabula->session->addError('Cannot delete your own user');
                header('Location: ' . $this->request->getSelf([],['action','id'],true),true,303);
                die();
            }
            $this->userModel->delete($id);
            header('Location: ' . $this->request->getSelf([],['action','id'],true),true,303);
            die();
            return '';
        }

        /**
         * Return the name of your admin pane,
         * for the menu
         */
        public function getName(): string{
            return "Users";
        }
    
        /**
         * Return a url-friendly slug for your pane
         */
        public function getSlug(): string{
            return "auth-users";
        }
    
        /**
         * Return an icon for the menu if you want to
         */
        public function getIcon(): ?string{
            return "users";
        }
    }
}