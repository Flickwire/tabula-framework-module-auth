<?php
namespace Tabula\Modules\Auth\Models;

use Tabula\Modules\Auth\User;

class Users{
    private $db;
    private $table = 'tb_users';

    public function __construct($db){
        $this->db = $db;
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

    public function newUser(string $name, string $email, string $password): string{

        //Try to use argon, if it's available
        $hash = @\password_hash($password,PASSWORD_ARGON2ID);
        //otherwise revert to default
        if (is_null($hash)){
            $hash = \password_hash($password,PASSWORD_DEFAULT);
        }
        unset($password);

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
}