<?php 
namespace Helpers;

use DateTime;
use Throwable;
use Model\Utility;

class OtpTokens extends Utility {

    protected $db, $table, $responseBody;

    public function __construct($db) {
        $this->db = $db;
        
        $this->table = new \stdclass();
        $this->table->otp_tokens = 'otp_tokens';
    }
    
    public function generateOtpCode() {
        try {
            return $this->randID('numeric', 6);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function createOtpToken($data) : bool {
        try {
            $userId = intval($data['userid']);
            $otpCode = intval($data['otp_code']);
            $useCase = strtolower($data['use_case']);

            $otpData = [
                'user_id' => $userId,
                'code' => $otpCode,
                'status' => 'new',
                'use_case' => $useCase,
                'additional_info' => array_key_exists('additional_info', $data) && is_array($data['additional_info']) 
                                        ? json_encode($data['additional_info']) 
                                        : ($data['additional_info'] ?? NULL)
            ];

            $createOtp = $this->db->insert($this->table->otp_tokens, $otpData);
            if ($createOtp) {
                $this->responseBody = true;
            } else {
                $this->responseBody = false;
            }

            return $this->responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function get_otp_token($useCase = 'mfa', $userId = "" , $otpCode = "") : array|null {
        try {
            $query = "select * from {$this->table->otp_tokens} where use_case = '{$useCase}'";

            if (!empty($userId)) {
                $query .= " AND user_id = '{$userId}'";
            }
            if (!empty($otpCode)) {
                $query .= " AND code = '{$otpCode}'";
            }
            $query .= " ORDER BY id DESC LIMIT 1";

            $result = $this->db->getRecFrmQry($query, 'single');
            return $result;
        } catch (Throwable $e) {
            throw $e;
        }  
    }

    public function checkTimeDuration($createdDate, $durationToCheck = 10) : bool {
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

    public function extendOtpTime($otpCode, $minute = 10) : bool {
        try {
            $otpInfo = $this->validateOtp($otpCode);
            if (!empty($otpInfo)) {
                // Create a DateTime object
                $date = new DateTime($otpInfo['created_at']);

                // Add minutes to the time...
                $date->modify("+$minute minutes");

                // Get the new date and time
                $newDateTime = $date->format('Y-m-d H:i:s');

                // Update the database
                $result = $this->db->update($this->table->otp_tokens, ['created_at' => $newDateTime], ["code" => $otpCode]);
                if ($result) {
                    $this->responseBody = true;
                } else {
                    $this->responseBody = false;
                }
                return $this->responseBody;
            }
            return false;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function updateToken($otpCode, $userId, $status = 'used') 
    {
        try {
            $result = $this->db->update($this->table->otp_tokens, ['status' => $status], ["code" => $otpCode, "user_id" => $userId]);
            if ($result) {
                $this->responseBody = true;
            } else {
                $this->responseBody = false;
            }
            return $this->responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function deleteAllOtp() : bool {
        try {
            $deleteOtps = $this->db->delete2("DELETE FROM {$this->table->otp_tokens}");
            
            if ($deleteOtps) {
                $this->responseBody = true;
            } else {
                $this->responseBody = false;
            }
            return $this->responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function deleteOtp($otpCode) : bool {
        try {
            $deleteOtp = $this->db->delete2("DELETE FROM {$this->table->otp_tokens} WHERE code = '$otpCode'");
            
            if ($deleteOtp) {
                $this->responseBody = true;
            } else {
                $this->responseBody = false;
            }
            return $this->responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function validateOtp($otp) : array|null {
        try {
            $result = $this->db->getSingleRecord($this->table->otp_tokens, "*", " AND code = '$otp' AND `status` = 'new'", " ORDER BY id desc");
            return $result;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getAllOtp() : array {
        try {
            $result = $this->db->getAllRecords($this->table->otp_tokens, "*", " ORDER BY id desc");
            return $result;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function getInvalidOtps() : array {
        try {
            $query = "SELECT * FROM {$this->table->otp_tokens} WHERE (status = 'expired' OR created_at <= DATE_ADD(NOW(), INTERVAL -10 MINUTE)) LIMIT 500";
            $result = $this->db->getRecFrmQry($query, 'multi');
            return $result;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}