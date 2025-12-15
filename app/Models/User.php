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

    public function getUser($userId) {
        try {
            return 'rayya';
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function getUserByEmail(string $email) : array {
        try {
            $user = $this->db->getSingleRecord($this->table->user, '*', " AND users_email = '$email'");

            if (!$user OR $user === null) {
                return [];
            }

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