<?php 

namespace Model;

use Model\Utility;
use stdClass;
use Throwable;

class User extends Utility {

    protected $table;
    
    public function __construct($db) {
        $this->db = $db;

        $this->table = new stdClass();
        $this->table->user = 'user';
        $this->table->user_details = 'user_details';

    }

    public function getUserPassword(string|int $userData) {
        try {
            if (is_int($userData)) {
                $user = $this->db->getSingleRecord($this->table->user, 'users_password', " AND user_id = '$userData'");
            } else {
                $user = $this->db->getSingleRecord($this->table->user, 'users_password', " AND users_email = '$userData'");
            }
            return $user['users_password'];
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getUser(string|int $userData) {
        try {
            if (is_int($userData)) {
                $user = $this->getUserById($userData);
            } else {
                $user = $this->getUserByEmail($userData);
            }
            return $user;
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    private function getUserByEmail(string $email) : array {
        try {
            $user = $this->db->getSingleRecord($this->table->user, '*', " AND users_email = '$email'");

            if (!$user OR $user === null) {
                return [];
            }

            unset($user['users_password']);
            return $user;

        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    private function getUserById(int $userId) : array {
        try {
            $user = $this->db->getSingleRecord($this->table->user, '*', " AND user_id = '$userId'");

            if (!$user OR $user === null) {
                return [];
            }

            unset($user['users_password']);
            return $user;

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function createUser(array $userData)  {

        try{
            $createUser = $this->db->insert($this->table->user,$userData);
            
            if($createUser){
                $this->responseBody = true;               
            } else {              
                $this->responseBody = false;
            }
            return $this->responseBody;
              
        } catch(Throwable $e){
                throw $e;
        }

    }
}