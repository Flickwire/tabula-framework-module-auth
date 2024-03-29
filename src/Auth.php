<?php
namespace Tabula\Modules\Auth;


use Tabula\Tabula;
use Tabula\Module;
use Tabula\Router;
use Tabula\Router\Route;
use Tabula\Database\Adapter\AbstractAdapter;
use Tabula\Modules\Auth\Pages\Login;
use Tabula\Modules\Auth\Pages\Register;

class Auth implements Module {
    private $user;

    //Can use this to check if user is logged in
    public $isLoggedIn = false;

    public function upgrade(string $version, AbstractAdapter $db): string{
        //Initial Setup
        if ($version === ''){
            //Set up db tables
            $db->query('CREATE TABLE tb_users (id BIGINT AUTO_INCREMENT NOT NULL, email NVARCHAR(320) NOT NULL, passwd VARCHAR(255) NOT NULL, displayname NVARCHAR(200) NOT NULL, CONSTRAINT PK PRIMARY KEY (id));');
            $db->query('CREATE TABLE tb_usergroups (id BIGINT AUTO_INCREMENT NOT NULL, displayname NVARCHAR(255) NOT NULL, CONSTRAINT PK PRIMARY KEY (id));');
            $db->query('CREATE TABLE tb_users_usergroups (user BIGINT NOT NULL, usergroup BIGINT NOT NULL, CONSTRAINT PK PRIMARY KEY (user,usergroup), CONSTRAINT FK_user FOREIGN KEY (user) REFERENCES tb_users(id), CONSTRAINT FK_usergroup FOREIGN KEY (usergroup) REFERENCES tb_usergroups(id));');
            $db->query('CREATE TABLE tb_users_permissions (id BIGINT AUTO_INCREMENT NOT NULL, user BIGINT NOT NULL, permission NVARCHAR(255), CONSTRAINT PK PRIMARY KEY (id), CONSTRAINT FK_userperms_user FOREIGN KEY (user) REFERENCES tb_users(id));');
            $db->query('CREATE TABLE tb_usergroups_permissions (id BIGINT AUTO_INCREMENT NOT NULL, usergroup BIGINT NOT NULL, permission NVARCHAR(255), CONSTRAINT PK PRIMARY KEY (id), CONSTRAINT FK_groupperms_usergroup FOREIGN KEY (usergroup) REFERENCES tb_usergroups(id));');
        
            $db->query('INSERT INTO tb_users (email, passwd, displayname) VALUES ("administrator","$argon2id$v=19$m=1024,t=2,p=2$UUxDd2xleVU2cUlqdEVwVQ$nCqeHS2fTuNPn9uoYliSbl8Epp/R8bEGoTP6w6qZdSo","Administrator");');

            return '1.0';
        }
        return $version;
    }

    public function registerRoutes(Router $router): void{
        $router->register(new Route("/login",$this,"renderLogin"));
        $router->register(new Route("/register",$this,"renderRegister"));
    }

    public function preInit(Tabula $tabula): void{
        $this->tabula = $tabula;
        $tabula->registry->setAuthHandler($this);
        $tabula->renderer->registerTemplateDir(__DIR__.DS.'templates');
        $tabula->renderer->registerScriptDir(__DIR__.DS.'scripts');
    }

    public function init(): void{
        if ($this->tabula->registry->hasAdminPanel()){
            $adminPane = $this->tabula->registry->getAdminPanel();
            $adminPane->registerPane(new \Tabula\Modules\Auth\Panes\UsersPane($this->tabula),'Auth');
            $adminPane->registerPane(new \Tabula\Modules\Auth\Panes\OptionsPane($this->tabula));
            //TODO: Register other admin panes
        }
        //Check if user is logged in
        if ($this->tabula->session->hasUserId()){
            $this->user = new User(['displayname'=>'Name','email'=>'yeet'],[],[]);
            $this->isLoggedIn = true;
        } else {
            $this->user = User::guest();
            $this->isLoggedIn = false;
        }
    }

    public function getName(): string{
        return 'tabula-auth';
    }

    public function renderLogin(): void{
        (new Login($this->tabula,$this))->render();
    }

    public function renderRegister(): void{
        (new Register($this->tabula,$this))->render();
    }
}
