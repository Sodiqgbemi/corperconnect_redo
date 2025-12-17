<?php 
namespace Helpers;

use Throwable;

class RateLimit {

    public function __construct() {
    }

    /**
     * Check if a user is within the allowed rate limit.
     * 
     * @param string $key Unique key to identify the rate limit (e.g., 'resend_otp').
     * @param int $interval Allowed interval in seconds.
     * @return array [bool $allowed, string|null $message] 
     */
    public static function checkRateLimit($key, $interval = 1) : array {
        try {
            if (!isset($_SESSION)) {
                throw new Exception("Sessions are not enabled or configured.");
            }
        
            // Convert interval to seconds
            $intervalSeconds = (int) $interval * 60;
            $rateLimitKey = "rate_limit_{$key}";
            $currentTime = time();
        
            // Check if a previous timestamp and interval exist for the key
            if (isset($_SESSION[$rateLimitKey])) {
                $rateLimitData = $_SESSION[$rateLimitKey];
                $lastRequestTime = $rateLimitData['timestamp'];
                $originalInterval = $rateLimitData['interval'];
        
                // Use the original interval set when the request was first made
                $originalIntervalSeconds = (int) $originalInterval * 60;
        
                // Calculate the time elapsed since the last request
                $elapsedTime = $currentTime - $lastRequestTime;
        
                // If the request is within the disallowed interval, block it
                if ($elapsedTime < $originalIntervalSeconds) {
                    $remainingTime = $originalIntervalSeconds - $elapsedTime;
                    $remainingMinutes = floor($remainingTime / 60);
                    $remainingSeconds = $remainingTime % 60;
        
                    return [
                        "status" => false,
                        "message" => "Please wait for {$remainingMinutes} minutes and {$remainingSeconds} seconds before retrying."
                    ];
                }
        
                // If time has elapsed, clear the session for the key
                unset($_SESSION[$rateLimitKey]);
            }
        
            // Update the session with the current timestamp and interval
            $_SESSION[$rateLimitKey] = [
                'timestamp' => $currentTime,
                'interval' => $interval
            ];
        
            // Allow the request
            return [
                "status" => true,
                "message" => null
            ];
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public static function clearRateLimit($key) : void {
        try {
            $rateLimitKey = "rate_limit_{$key}";

            if (isset($_SESSION[$rateLimitKey])) {
                unset($_SESSION[$rateLimitKey]);
            }
            
        } catch(Throwable $e) {
            throw $e;
        }
    }

}