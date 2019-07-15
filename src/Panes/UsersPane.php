<?php
namespace Tabula\Modules\Auth\Panes;

if (interface_exists("\Tabula\Modules\Admin\AdminPane")){
    class UsersPane implements \Tabula\Modules\Admin\AdminPane {
        private $tabula;

        public function render(\Tabula\Tabula $tabula): string{
            $this->tabula = $tabula;
            $action = $tabula->registry->getRequest()->get('action');
            switch($action){
                case 'create':
                    $outMarkup = file_get_contents(__DIR__.DS."html".DS."newUser.html");
                    return $outMarkup;
                case 'edit':
                    //$outMarkup = file_get_contents(__DIR__.DS."html".DS."newUser.html");
                    //return $outMarkup;
                    return 'edit';
                case 'delete':
                    //$outMarkup = file_get_contents(__DIR__.DS."html".DS."newUser.html");
                    //return $outMarkup;
                    return 'delete';
                default:
                    return $this->listUsers();
            }
        }

        private function listUsers(): string{
            $outMarkup = file_get_contents(__DIR__.DS."html".DS."listUsers.html");
            $users = $this->tabula->db->query("SELECT id, displayname, email FROM tb_users")->fetchAll();
            foreach ($users as $user){
                $outMarkup = str_replace("_{USERS}_","
                <tr>
                <td>{$user['displayname']}</td>
                <td>{$user['email']}</td>
                <td><div class=\"ui small negative right floated button\">
                    Delete
                </div><div class=\"ui small right floated button\">
                    Edit
                </div></td>
                </tr>
                _{USERS}_
                ",$outMarkup);
            }
            $outMarkup = str_replace("_{USERS}_","",$outMarkup);
            return $outMarkup;
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
            return "auth/users";
        }
    
        /**
         * Return an icon for the menu if you want to
         */
        public function getIcon(): ?string{
            return "users";
        }
    }
}