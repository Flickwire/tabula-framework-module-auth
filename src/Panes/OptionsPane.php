<?php
namespace Tabula\Modules\Auth\Panes;

//use Tabula\Models\Options;

if (interface_exists("\Tabula\Modules\Admin\AdminPane")){
    class OptionsPane implements \Tabula\Modules\Admin\AdminPane {
        private $tabula;
        private $request;
        //private $optionsModel;

        public function render(\Tabula\Tabula $tabula): string{
            $this->tabula = $tabula;
            $this->request = $this->tabula->registry->getRequest();
            //$this->optionsModel = new Options($tabula->db);

            $outMarkup = \file_get_contents(__DIR__.DS."html".DS."options.html");
            return $outMarkup;
        }

        /**
         * Return the name of your admin pane,
         * for the menu
         */
        public function getName(): string{
            return "Options";
        }
    
        /**
         * Return a url-friendly slug for your pane
         */
        public function getSlug(): string{
            return "auth-options";
        }
    
        /**
         * Return an icon for the menu if you want to
         */
        public function getIcon(): ?string{
            return "cog";
        }
    }
}