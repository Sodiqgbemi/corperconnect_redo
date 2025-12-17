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

    public function getUser(string|int $userData) : array {
        try {
            if ( is_string($userData)){
                return $this->db->getUserByEmail($userData);
            }
             else {
                return $this->db->getUserById($userData);
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getUserPassword(){
        try{
            $user = $this->db->getSingleRecord($this->table->user, '*', " AND users_email = '$email'");

            if (!$user OR $user === null) {
                return [];
            }

            return $user;

        } catch (Throwable $e) {
            throw $e;
        }
    }

    
    private function getUserByEmail(string $userData) : array {
        try {
            $user = $this->db->getSingleRecord($this->table->user, '*', " AND users_email = '$userData'");

            if (!$user OR $user === null) {
                return [];
            }
            return $user;

        } catch (Throwable $e) {
            throw $e;
        }
    }


    private function getUserById(Int $userData){
         try {
            $query = "SELECT *, FROM 
                {$this->table->user} user
                WHERE 
                (user.user_id = '$userData')
            ";
            $result = $this->db->getRecFrmQry($query, 'single');
            if ($result != NULL) {
                $this->responseBody = $result;
            } else {
                $this->responseBody = [];
            }
            return $this->responseBody;
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