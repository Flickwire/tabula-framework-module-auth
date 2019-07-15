<?php
namespace Tabula\Modules\Auth\Panes;

if (interface_exists(\Tabula\Modules\Admin\AdminPane)){
    class UsersPane implements \Tabula\Modules\Admin\AdminPane {
        public function render(\Tabula\Tabula $tabula): string{
            return "Users Pane";
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
            return "admin/users";
        }
    
        /**
         * Return an icon for the menu if you want to
         */
        public function getIcon(): ?string{
            return "users";
        }
    }
}