<?php
namespace Tabula\Modules\Auth;

class User {
    private $displayname;
    private $email;

    private $permissions;
    private $groups;

    public function __construct($displayname, $email){
        $this->displayname = $displayname;
        $this->email = $email;
    }

    public function getName(){
        return $this->displayname;
    }

    public function getEmail(){
        return $this->email;
    }

    public function can(string $permission){
        $this->preparePermissions();
        $this->prepareGroups();
        $levels = \explode('.',$permission);
        $permissions = $this->permissions;
        foreach($levels as $level){
            //Wildcard permission
            if(\array_key_exists('*',$permissions)){
                return true;
            }
            //Check specific permission at this level
            if(!\array_key_exists($level,$permissions)){
                return false;
            }
            //Next permission level
            $permissions = $permissions[$level];
        }
        return true;
    }

    private function preparePermissions(): void{
        //Only fire once per request
        if (!is_null($this->permissions)){
            return;
        }
        $permissions = $this->model->getPermissions();
        $pTree = [];
        foreach ($permissions as $permission){
            $subTree = &$pTree;
            $levels = \explode('.',$permission);
            foreach ($levels as $level){
                if(!\array_key_exists($level,$subTree)){
                    $subTree[$level] = [];
                }
                $subTree = &$subTree[$level];
            }
            unset($subTree);
        }
        $this->permissions = $pTree;
    }
}