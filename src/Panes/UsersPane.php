<?php
namespace Tabula\Modules\Auth\Panes;

if (interface_exists("\Tabula\Modules\Admin\AdminPane")){
    class UsersPane implements \Tabula\Modules\Admin\AdminPane {
        public function render(\Tabula\Tabula $tabula): string{
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
                    $outMarkup = file_get_contents(__DIR__.DS."html".DS."listUsers.html");
                    return $outMarkup;
            }
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