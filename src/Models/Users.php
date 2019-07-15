<?php
namespace Tabula\Modules\Auth\Models;

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
        return $this->db->query($query,$id);
    }
}