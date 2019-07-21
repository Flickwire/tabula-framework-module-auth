<?php
namespace Tabula\Modules\Auth\Models;

use Tabula\Modules\Auth\User;

class Users{
    private $tabula;
    private $db;
    private $table = 'tb_users';

    public function __construct($tabula){
        $this->db = $tabula->db;
        $this->tabula = $tabula;
    }

    public function getUsers(int $offset = 0, int $limit = 0){
        $query = "SELECT id, displayname, email FROM {$this->table}";
        if ($limit !== 0){
            $query .= " LIMIT ?i";
            if ($offset !== 0){
                $query .= " OFFSET ?i";
                return $this->db->query($query,$limit,$offset);
            }
            return $this->db->query($query,$limit);
        }
        return $this->db->query($query);
    }

    public function get(int $id){
        $query = "SELECT id, displayname, email FROM {$this->table} WHERE id = ?i";
        $result = $this->db->query($query,$id)->fetch();
        if (!$result){
            return null;
        }
        return new User($result['id'],$result['displayname'],$result['email']);
    }

    public function hashPassword(string $password){
        //Try to use argon, if it's available
        $hash = @\password_hash($password,PASSWORD_ARGON2ID);
        //otherwise revert to default
        if (is_null($hash)){
            $hash = \password_hash($password,PASSWORD_DEFAULT);
        }
        return $hash;
    }

    public function newUser(string $name, string $email, string $password): string{

        $hash = $this->hashPassword($password);

        $name = \htmlspecialchars($name);
        $email = \htmlspecialchars($email);

        $query = "INSERT INTO {$this->table}(displayname, email, passwd) VALUES (?s,?s,?s)";

        $this->db->query($query,$name,$email,$hash);
        return $this->db->lastInsertId();
    }

    public function delete($id){
        $query = "DELETE FROM {$this->table} WHERE id = ?i";
        $this->db->query($query,$id);
    }

    public function login(string $email, string $password){
        $user = $this->tabula->db->query("SELECT * FROM tb_users WHERE email = ?s",$email)->fetch();
        if (!$user) {
            return false;
        }
        //check password
        $verify = password_verify($password, $user['passwd']);
        unset($password, $user['passwd']);
        if (!$verify) {
            return false;
        }
        $this->tabula->session->setUserId($user['id']);
        return true;
    }

    public function emailUsed(string $email, $id = null){
        $user = $this->tabula->db->query("SELECT id FROM tb_users WHERE email = ?s",$email)->fetch();
        if (!$user) {
            return false;
        }
        if(!is_null($id) && $id = $user['id']){//Existing user can keep using their current email
            return false;
        }
        return true;
    }

    
    public function validateUser($name, $email, $password, $password2, $id = null){
        $session = $this->tabula->session;

        if($this->emailUsed($email,$id)){
            $session->addError('A user with the provided email address already exists');
            return false;
        }

        //password too short or absent
        if(\is_null($password) || \strlen($password) < 12){
            if (is_null($id)){ //If we are updating a user they can omit their password
                $session->addError('Your password must be at least 12 characters long');
                return false;
            }
        } else {

            //password too short
            if(\strlen($password) > 128){
                $session->addError('Your password may not be longer than 128 characters');
                return false;
            }

            //password special character
            if(!\preg_match('/[!"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~\\\\]/',$password)){
                $session->addError('Your password must contain a special character<br />(!"#$%&amp;\'()*+,-./:;&lt;=&gt;?@[]^_`{|}~\\)');
                return false;
            }

            //password uppercase character
            if(!\preg_match('/[A-Z]/',$password)){
                $session->addError('Your password must contain at least one uppercase character');
                return false;
            }

            //password lowercase character
            if(!\preg_match('/[a-z]/',$password)){
                $session->addError('Your password must contain at least one lowercase character');
                return false;
            }

            //password number
            if(!\preg_match('/[0-9]/',$password)){
                $session->addError('Your password must contain at least one number');
                return false;
            }

            //Password doesn't match
            if($password !== $password2){
                $session->addError('Your passwords must match');
                return false;
            }

        }

        //email format
        if(!\preg_match('/[^@]+@[^\.]+\..+/',$email)){
            $session->addError('Please enter a valid email address');
            return false;
        }

        //password too short
        if(\is_null($name) || \strlen($name) < 1){
            $session->addError('Please enter your name as you would like it displayed');
            return false;
        }

        return true;
    }

    public function loadUser($id){
        $query = "SELECT displayname, email FROM {$this->table} WHERE id = ?s";
        return $this->db->query($query,$id)->fetch();
    }

    public function updateUser($id,$name,$email,$password){
        $query = "UPDATE {$this->table} SET displayname = ?s, email = ?s, passwd = ?s WHERE id = ?s";
        $queryNoPasswd = "UPDATE {$this->table} SET displayname = ?s, email = ?s WHERE id = ?s";
        if(is_null($password) || $password === ''){
            $this->db->query($queryNoPasswd,$name,$email,$id);
        } else {
            $hash = $this->hashPassword($password);
            $this->db->query($query,$name,$email,$hash,$id);
        }
    }
}