<?php 

namespace Model;

use Model\Utility;
use stdClass;
use Throwable;

class ResetRequest extends Utility {

    protected $table;
    
    public function __construct($db) {
        $this->db = $db;

        $this->table = new stdClass();
        $this->table->reset_requests = 'reset_requests';

    }

    public function create_request(array $requestData) {
        try {
            $result = $this->db->insert($this->table->reset_requests, $requestData);
            
            if($result){
                $this->responseBody = true;               
            } else {              
                $this->responseBody = false;
            }
            return $this->responseBody;

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getUserRequestLink(int $userId) : array {
        try {
            $requestInfo = $this->db->getSingleRecord($this->table->reset_requests, '*', " AND user_id = '$userId'");

            if (!$requestInfo OR $requestInfo === null) {
                return [];
            }
            
            return $requestInfo;

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function check_user_request_link_exists(int $userId) : bool {
        try {
            $result = $this->getUserRequestLink($userId);
            if (empty($result)) {
                return false;
            }

            return true;

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function checkTimeDuration($createdDate, $durationToCheck = 10): bool
    {
        try {
            // Convert the created time to a Unix timestamp
            $createdTimestamp = strtotime($createdDate);

            // Get the current Unix timestamp
            $currentTimestamp = time();

            // Calculate the difference in seconds
            $difference = $currentTimestamp - $createdTimestamp;

            // Check if the difference is less than minutes specified (convert to seconds)
            $minuteCheckToSeconds = intval($durationToCheck * 60);

            if ($difference < $minuteCheckToSeconds) {
                return true;
            } else {
                return false;
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function delete_request($requestId): bool
    {
        try {
            $result = $this->db->delete($this->table->reset_requests, ['request_id' => $requestId]);
            if ($result > 0) {
                return true;
            }
            return false;
        } catch (Throwable $e) {
            throw $e;
        }
    }

}