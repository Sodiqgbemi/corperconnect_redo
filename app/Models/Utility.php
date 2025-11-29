<?php

namespace Model;

use DateTime;
use Throwable;
use Model\ApiToken;
use Model\Settings;
use Helpers\Paystack;
use Includes\Database;
use Model\PayoutBanks;
use Helpers\Flutterwave;
use Includes\ClientLang;
use Model\PaymentGateways;
use InvalidArgumentException;

class Utility extends Database
{

    protected $responseBody, $db;
    public $pageLimit;

    function __construct($db)
    {
        $this->db = $db;
        $this->responseBody    = array();
        $this->pageLimit = 20;
    }

    /**
     * convert objects to array
     *
     * @param array $array
     * @return object
     */
    public function arrayToObject($array)
    {
        return (object) $array;
    }

    /**
     * convert arrays to object
     *
     * @param object $object
     * @return array
     */
    public function objectToArray($object)
    {
        return (array) $object;
    }

    public function niceDateFormat($date, $format = "date_time")
    {

        if ($format == "date_time") {
            $format = "D j, M Y h:ia";
        } else {
            $format = "D j, M Y";
        }

        $timestamp = strtotime($date);
        $niceFormat = date($format, $timestamp);

        return $niceFormat;
    }

    public function timeNow($type = 'date') {
        if ($type == 'date') {
            return date('Y-m-d');
        } elseif ($type == 'datetime') {
            return date('Y-m-d H:i:s');
        }
    }

    public function generateReference()
    {
        return date("YmdHis") . $this->randID('numeric', 2); // 2024101312491010
    }

    public function randID(string $character, int $length = 5): string
    {

        $numericChars = '0123456789';
        $alphaChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphanumericChars = $numericChars . $alphaChars;

        $idNumber = '';

        switch (strtolower($character)) {
            case 'numeric':
                $min = 10 ** ($length - 1);
                $max = (10 ** $length) - 1;
                $idNumber = (string) random_int($min, $max);
                break;

            case 'alphabetic':
            case 'alpha':
                for ($i = 0; $i < $length; $i++) {
                    $idNumber .= $alphaChars[random_int(0, strlen($alphaChars) - 1)];
                }
                break;

            case 'alphanumeric':
                for ($i = 0; $i < $length; $i++) {
                    $idNumber .= $alphanumericChars[random_int(0, strlen($alphanumericChars) - 1)];
                }
                break;

            default:
                throw new InvalidArgumentException('Unsupported character type. Use "numeric", "alphabetic", or "alphanumeric".');
        }

        return $idNumber;
    }

    public function generateApiToken() {
        return $this->randID('alphanumeric', 64);
    }

    public function returnFormInput($name)
    {
        $formInput = '';
        if (isset($_SESSION['formInput'][$name])) {
            $formInput = $_SESSION['formInput'][$name];
            unset($_SESSION['formInput'][$name]);
        }
        echo $formInput;
    }

    public function displayAlertMessage() {
        $formError = "";
        if (!isset($_SESSION['titleMessage'])) {
            if (isset($_SESSION['errorMessage'])) {
                $formError .= '<div class="alert alert-danger alert-dismissible d-flex" role="alert">
                                    <div>';
                if (is_array($_SESSION['errorMessage'])) {
                    $formError .= '<h5 class="mt-1 mb-2">Errors!</h5>
                        <ul style="list-style: none">';
                    foreach ($_SESSION['errorMessage'] as $key => $error) {
                        $formError .= '<li>' . $error . '</li>';
                    }
                    $formError .= '</ul>';
                } else {
                    $formError .= '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>';
                    $formError .= '<strong>Error:</strong> ' . $_SESSION['errorMessage'];
                }
                $formError .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>';

                unset($_SESSION['errorMessage']);
                return $formError;
            }

            if (isset($_SESSION['successMessage'])) {
                $formError .= '<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">';
                if (is_array($_SESSION['successMessage'])) {
                    $formError .= '<div>
                        <h5 class="mt-1 mb-2">Success!</h5>
                        <ul style="list-style: none">';
                    foreach ($_SESSION['successMessage'] as $key => $error) {
                        $formError .= '<li>' . $error . '</li>';
                    }
                    $formError .= '</ul></div>';
                } else {
                    $formError .= '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                <div>';
                    $formError .= '<strong>Success:</strong> ' . $_SESSION['successMessage'];
                    $formError .= '</div>';
                    $formError .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                }
                $formError .= '</div>';

                unset($_SESSION['successMessage']);
                $this->clearFormSessions();
                return $formError;
            }
        } else {
            
            if(isset($_SESSION['errorMessage'])) { ?>
                <script>
                    Swal.fire({
                        icon: "error",
                        title: "<?php echo $_SESSION['titleMessage'];?>",
                        html: "<?php echo $_SESSION['errorMessage'];?>"
                    });
                </script>
            <?php 
                unset($_SESSION['errorMessage']);
            } 

            if(isset($_SESSION['successMessage'])) { ?>
                <script>
                    Swal.fire({
                        icon: "success",
                        title: "<?php echo $_SESSION['titleMessage'];?>",
                        html: "<?php echo $_SESSION['successMessage'];?>"
                    });
                </script>
            <?php
                unset($_SESSION['successMessage']);
                $this->clearFormSessions();
            }

            if(isset($_SESSION['infoMessage'])) { ?>
                <script>
                    Swal.fire({
                        icon: "info",
                        title: "<?php echo $_SESSION['titleMessage'];?>",
                        html: "<?php echo $_SESSION['infoMessage'];?>"
                    });
                </script>
            <?php 
                unset($_SESSION['infoMessage']);
            } 

            unset($_SESSION['titleMessage']);
        }
    }
    
    public function clearFormSessions()
    {
        if (isset($_SESSION['formInput'])) {
            unset($_SESSION['formInput']);
        }
        if (isset($_SESSION['csrf_token'])) {
            unset($_SESSION['csrf_token']);
        }
        if (isset($_SESSION['csrf_token_expiration'])) {
            unset($_SESSION['csrf_token_expiration']);
        }
        if (isset($_SESSION['errorMessage'])) {
            unset($_SESSION['errorMessage']);
        }
    }

    public static function log_txt(string $logFile, string|array|null $dataToLog, string $filePath)
    {
        // Define the full path to store today's logs
        $todayStoragePath = ROOT . "/" . $filePath . "/" . date("Y-m-d") . "/";

        // Check if the directory exists, if not, create it with the appropriate permissions
        if (!is_dir($todayStoragePath)) {
            mkdir($todayStoragePath, 0755, true); // Declare file permission, set recursive to true
        }

        // Define the full log file path
        $logFile = $todayStoragePath . $logFile . ".txt";

        // Ensure the file exists, if not, create an empty file
        if (!file_exists($logFile)) {
            touch($logFile); // Creates an empty file
        }

        // Convert array to JSON string, or just use the string as-is
        $data = is_array($dataToLog) ? json_encode($dataToLog) : $dataToLog;

        // Prepare log contents with timestamp
        $arrayContents = [
            "==========" . date("H:i:s") . "============",
            $data,
            "===========end============",
        ];

        // Write each content line to the log file, appending it
        foreach ($arrayContents as $content) {
            file_put_contents($logFile, $content . "\r\n", FILE_APPEND);
        }
    }

    public function transformRemoveDuplicate($receiver)
    {
        try {

            //Convert the recipient...
            $recipient_explode = explode(",", str_replace("'", '', $receiver));
            $validNumber = [];

            foreach ($recipient_explode as $recipientExplode) {
                //We need to remove spaces and symbols out...
                $filterNo = str_replace(array(" ", "+", "'", "/", '"'), "", $recipientExplode);

                if (substr($filterNo, 0, 1) == 0 and strlen($filterNo) == 11) {
                    $validNumber[] = '234' . substr($filterNo, 1);
                } else if (strlen($filterNo) >= 11) {
                    $validNumber[] = $recipientExplode;
                }
            }

            if (!empty($validNumber)) {
                $responseBody = implode(",", array_unique($validNumber));
            } else {
                $responseBody = false;
            }

            return $responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function isEmail($data): bool
    {
        try {
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function userActivePageClass(string|array $url = 'dashboard')
    {
        return (is_array($url) AND in_array(PAGE, $url)) ? 'active' : (PAGE === $url ? 'active' : '');
    }

    public function paymentProviderImages(string $gateway_code)
    {
        switch ($gateway_code) {
            case "paystack":
                $imagePath = BASE_URL . "assets/img/paymentgateways/paystack.png";
                break;
            case "flutterwave":
                $imagePath = BASE_URL . "assets/img/paymentgateways/flutterwave_logo.png";
                break;
            default:
                $imagePath = BASE_URL . "assets/img/paymentgateways/default.png";
                break;
        }

        return $imagePath;
    }

    // Use to check if input has no space and consist of a number minimum, then meet the number of length too
    public function validateInput($inputValue, $minLength = 5)
    {
        // Regular expression to check:
        // ^ = start of string
        // (?=.*\d) = must contain at least one digit
        // \S{minLength,} = no spaces, and at least minLength characters long
        $regex = '/^(?=.*\d)\S{' . $minLength . ',}$/';

        // Test the input value against the regular expression
        if (preg_match($regex, $inputValue)) {
            return true;
        } else {
            return false;
        }
    }

    function removeArrayIndexes(&$array, $indexes)
    {
        foreach ($indexes as $index) {
            if (isset($array[$index])) {
                unset($array[$index]); // Remove the index
            }
        }
    }

    public function hashPassword(string $password) {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
            $this->responseBody = $hash;
            return $this->responseBody;
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function calcTimeLeftInMin($unblockTime) {
        try {
            // Convert the unblock_time to a DateTime object
            $unblockTime = new DateTime($unblockTime);
            
            // Get the current time
            $currentTime = new DateTime();
    
            // Check if the unblock time has already passed
            if ($currentTime >= $unblockTime) {
                return false; // Return message indicating the account is unblocked
            }
    
            // Calculate the difference between the unblock_time and current time
            $interval = $currentTime->diff($unblockTime);
    
            // Calculate total minutes left
            $totalMinutesLeft = ($interval->h * 60) + $interval->i; // Convert hours to minutes and add remaining minutes
    
            // Format the duration left as minutes and seconds
            $durationLeft = sprintf('%d minutes and %d seconds', $totalMinutesLeft, $interval->s);
    
            return $durationLeft;
    
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function slugify($text) {
        try {
            $text = str_replace(array("'","_", "."), "", $text);
            $text = str_replace(array(" "), "_", $text);
            return strtolower($text);
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function check_date($given_date, $checkType = 'future_date') {
        try {
            $date = DateTime::createFromFormat('Y-m-d', $given_date);
            if ($date && $date->format('Y-m-d') === $given_date) {
                $rephrased_date = $date->format('Y-m-d');
                if ($checkType == 'future_date') {
                    return strtotime($rephrased_date) >= strtotime(date("Y-m-d")) ? true : false;
                }

                if ($checkType == 'past_date') {
                    return strtotime($rephrased_date) < strtotime(date("Y-m-d")) ? true : false;
                }
            } else {
                return "Invalid date format.";
            }
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function check_date_time($given_date_time, $checkType = 'future_date') {
        try {
            // Parse the given date and time string into a DateTime object
            $dateTime = DateTime::createFromFormat('Y-m-d H:i', $given_date_time);
    
            if ($dateTime && $dateTime->format('Y-m-d H:i') === $given_date_time) {
                // Current date and time
                $currentDateTime = new DateTime();
    
                if ($checkType == 'future_date') {
                    return $dateTime > $currentDateTime;
                }
    
                if ($checkType == 'past_date') {
                    return $dateTime < $currentDateTime;
                }
            } else {
                return "Invalid date-time format.";
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function leagueStatusHtml($status) {
        $status = strtolower($status);
        if ($status === 'active') {
            return '<span class="badge badge-success">Active</span>';
        }
        if ($status === 'onhold') {
            return '<span class="badge badge-warning">On Hold</span>';
        }
        if ($status === 'closed') {
            return '<span class="badge badge-danger">Closed</span>';
        }
    }

    public function betSlipStatusHtml($status) {
        $status = strtolower($status);
        if ($status === 'running') {
            return '<span class="text-primary">Running</span>';
        }
        if ($status === 'won') {
            return '<span class="text-success">Won</span>';
        }
        if ($status === 'loss') {
            return '<span class="text-danger">Lost</span>';
        }
        if ($status === 'cancelled') {
            return '<span class="text-danger">Cancelled</span>';
        }
        if ($status === 'void') {
            return '<span class="text-info">Void</span>';
        }
    }
    
    public function encryptData($data, $cipher = 'AES-256-CBC') {
        $iv_length = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($data, $cipher, ENCRYPT_CODE, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    public function decryptData($encryptedData, $cipher = 'AES-256-CBC') {
        try {
            // Validate the input format
            $decodedData = base64_decode($encryptedData, true);
            if ($decodedData === false || strpos($decodedData, '::') === false) {
                $this->log_txt('decrypt_data', 'Invalid encrypted data format: '. $encryptedData, 'logs');
                return null;
            }
    
            // Extract the encrypted string and IV
            list($encrypted, $iv) = explode('::', $decodedData, 2);
    
            // Validate IV length
            $ivLength = openssl_cipher_iv_length($cipher);
            if (strlen($iv) !== $ivLength) {
                $this->log_txt('decrypt_data', 'Invalid IV length: '. $encryptedData, 'logs');
                return null;
            }
    
            // Decrypt the data
            $decrypted = openssl_decrypt($encrypted, $cipher, ENCRYPT_CODE, 0, $iv);
            if ($decrypted === false) {
                $this->log_txt('decrypt_data', 'Decryption failed: '. $encryptedData, 'logs');
                return null;
            }
    
            return $decrypted;
    
        } catch (Throwable $e) {
            $this->log_txt('decrypt_data', 'Decryption Error: ' . $e->getMessage(), 'logs');
            return null;
        }
    }

    public function formatNumber($number)
    {
        try {
            $suffix = '';
            if ($number >= 1000000000000000) {
                $number = $number / 1000000000000000;
                $suffix = 'Q'; //Quadrillion
            }
            else if ($number >= 1000000000000) {
                $number = $number / 1000000000000;
                $suffix = 'T';
            }
            else if ($number >= 1000000000) {
                $number = $number / 1000000000;
                $suffix = 'B';
            } elseif ($number >= 1000000) {
                $number = $number / 1000000;
                $suffix = 'M';
            } elseif ($number >= 1000) {
                $number = $number / 1000;
                $suffix = 'K';
            }
        
            return number_format($number, 2) . $suffix;
        } catch (Throwable $e) {
            throw $e;   
        }
    }
    
    public function get_current_user($type = 'user', $apiKey = '') {
        try {
            if ($type == 'user') {
                if(empty($apiKey) AND isset($_SESSION['userid'])) {
                    return (int) $_SESSION['userid'];
                } else if (!empty($apiKey)) {
                    $apitoken_instance = new ApiToken($this->db);
                    $userId = $apitoken_instance->getUserIdByToken($apiKey);
                    return $userId;
                }            
                return '';
            } else if ($type == 'admin') {
                if(isset($_SESSION['adminid'])) {
                    return (int) $_SESSION['adminid'];
                }           
                return '';
            }
        } catch (Throwable $e) {
            throw $e;   
        }
    }
    
    public function revokeUnauthorize($type = 'user') {
        try {
            if ($type == 'admin') {
                if(isset($_SESSION['adminid'])) {
                    unset($_SESSION['adminid']);
                }
                header('location: '. ADMIN_AUTH_URL.'login');
                exit;
            } else {
                if(isset($_SESSION['userid'])) {
                    unset($_SESSION['userid']);
                }
                header('location: '. AUTH_URL.'login');
                exit;
            }
        } catch (Throwable $e) {
            throw $e;   
        }
    }

    public function isStrictlyNumber($input) : bool {
        return preg_match('/^\d+(\.\d+)?$/', $input);
    }

    public function walletStatusContent($status, $type = 'badge') {
        if($type == 'badge') {
            if($status == "0") {
                $spanBtn = "<span class='badge bg-primary p-2'>Pending</span>";
            }
            else if($status == "1") {
                $spanBtn = "<span class='badge bg-success p-2'>Approved</span>";
            }
            else if($status == "2") {
                $spanBtn = "<span class='badge bg-danger' p-2>Declined</span>";
            }
            else if($status == "3") {
                $spanBtn = "<span class='badge bg-warning p-2'>Wallet Refunded</span>";
            }
            else {
                $spanBtn = "<span class='badge bg-dark p-2'>Unknown Status</span>";
            }
            return $spanBtn;
        } else {
            if($status == "0") {
                $spanBtn = "<strong class='text-primary'>Pending</strong>";
            }
            else if($status == "1") {
                $spanBtn = "<strong class='text-success'>Approved</strong>";
            }
            else if($status == "2") {
                $spanBtn = "<strong class='text-danger'>Declined</strong>";
            }
            else if($status == "3") {
                $spanBtn = "<strong class='text-warning'>Wallet Refunded</strong>";
            }
            else {
                $spanBtn = "<span class='text-dark'>Unknown Status</span>";
            }
            return $spanBtn;
        }
    }

    public function transactStatusContent($status, $type = 'badge') {
        if($type == 'badge') {
            if($status == "0") {
                $spanBtn = "<span class='badge bg-primary p-2'>Pending</span>";
            }
            else if($status == "1") {
                $spanBtn = "<span class='badge bg-success p-2'>Completed</span>";
            }
            else if($status == "2") {
                $spanBtn = "<span class='badge bg-danger' p-2>Awaiting Delivery Response</span>";
            }
            else if($status == "3") {
                $spanBtn = "<span class='badge bg-warning p-2'>Wallet Refunded</span>";
            }
            else {
                $spanBtn = "<span class='badge bg-dark p-2'>Unknown Status</span>";
            }
            return $spanBtn;
        } else {
            if($status == "0") {
                $spanBtn = "<strong class='text-primary'>Pending</strong>";
            }
            else if($status == "1") {
                $spanBtn = "<strong class='text-success'>Completed</strong>";
            }
            else if($status == "2") {
                $spanBtn = "<strong class='text-danger'>Awaiting Delivery Response</strong>";
            }
            else if($status == "3") {
                $spanBtn = "<strong class='text-warning'>Wallet Refunded</strong>";
            }
            else {
                $spanBtn = "<span class='text-dark'>Unknown Status</span>";
            }
            return $spanBtn;
        }
    }

    public function verificationStatus($status, $type = 'badge') {
        if($type == 'badge') {
            if($status == "1") {
                $spanBtn = "<span class='badge bg-success p-2'>Account Verified</span>";
            }
            else {
                $spanBtn = "<span class='badge bg-dark p-2'>Unverified</span>";
            }
            return $spanBtn;
        } else {
            if($status == "1") {
                $spanBtn = "<strong class='text-success'>Account Verified</strong>";
            }
            else {
                $spanBtn = "<span class='text-dark'>Unverified</span>";
            }
            return $spanBtn;
        }
    }

    public function accountStatus($status, $type = 'badge') {
        if($type == 'badge') {
            if($status == "active") {
                $spanBtn = "<span class='badge bg-success p-2'>Account Active</span>";
            }
            else if($status == "suspended") {
                $spanBtn = "<span class='badge bg-danger p-2'>Account Suspended</span>";
            }
            else {
                $spanBtn = "<span class='badge bg-dark p-2'>Account Blocked</span>";
            }
            return $spanBtn;
        } else {
            if($status == "active") {
                $spanBtn = "<strong class='text-success'>Account Active</strong>";
            }
            else if($status == "suspended") {
                $spanBtn = "<strong class='text-success'>Account Suspended</strong>";
            }
            else {
                $spanBtn = "<span class='text-dark'>Account Blocked</span>";
            }
            return $spanBtn;
        }
    }
    
    public function kycVerificationStatus($bvnStatus, $ninStatus, $type = 'badge') {
        $bvnStatus = strtolower($bvnStatus);
        $ninStatus = strtolower($ninStatus);
        if ($type == 'badge') {
            if ($bvnStatus == "done" && $ninStatus == "done") {
                $spanBtn = "<span class='badge bg-success p-2'>Fully Verified</span>";
            }
            else if ($bvnStatus == "done" && $ninStatus == "pending") {
                $spanBtn = "<span class='badge bg-primary p-2'>BVN Verified Only</span>";
            }
            else if ($bvnStatus == "pending" && $ninStatus == "done") {
                $spanBtn = "<span class='badge bg-primary p-2'>NIN Verified Only</span>";
            }
            else {
                $spanBtn = "<span class='badge bg-info p-2'>Pending Verification</span>";
            }
            return $spanBtn;
        } else {
            if ($bvnStatus == "done" && $ninStatus == "done") {
                $spanBtn = "<strong class='text-success'>Fully Verified</strong>";
            }
            else if ($bvnStatus == "done" && $ninStatus == "pending") {
                $spanBtn = "<strong class='text-primary'>BVN Verified Only</strong>";
            }
            else if ($bvnStatus == "pending" && $ninStatus == "done") {
                $spanBtn = "<strong class='text-primary'>NIN Verified Only</strong>";
            }
            else {
                $spanBtn = "<span class='text-info'>Pending Verification</span>";
            }
            return $spanBtn;
        }
    }

    public function productAvailability($status) {
        if($status == "0") {
            $spanBtn = "<span class='badge bg-danger'>Downtime</span>";
        }
        else if($status == "1") {
            $spanBtn = "<span class='badge bg-success'>Available</span>";
        }
        else if($status == "2") {
            $spanBtn = "<span class='badge bg-warning'>Fair</span>";
        }
        return $spanBtn;
    }
    
    public function getProductImage($product) {
        if(strpos(strtolower($product), 'mtn') !== false) {
            return 'assets/images/product/mtn.jpg';
        } else if(strpos(strtolower($product), 'airtel') !== false) {
            return 'assets/images/product/airtel.jpg';
        } else if(strpos(strtolower($product), 'glo') !== false) {
            return 'assets/images/product/glo.jpg';
        } else if(strpos(strtolower($product), '9mobile') !== false) {
            return 'assets/images/product/9mobile.jpg';
        } else if(strpos(strtolower($product), 'gotv') !== false) {
            return 'assets/images/product/gotv.jpg';
        } else if(strpos(strtolower($product), 'dstv') !== false) {
            return 'assets/images/product/dstv.jpg';
        } else if(strpos(strtolower($product), 'star') !== false) {
            return 'assets/images/product/startimes.png';
        } else if(strpos(strtolower($product), 'waec') !== false) {
            return 'assets/images/product/waec.jpg';
        } else if(strpos(strtolower($product), 'neco') !== false) {
            return 'assets/images/product/neco.png';
        } else if(strpos(strtolower($product), 'ibedc') !== false) {
            return 'assets/images/product/ibedc.png';
        } else if(strpos(strtolower($product), 'phedc') !== false) {
            return 'assets/images/product/phedc.png';
        } else if(strpos(strtolower($product), 'kaedc') !== false) {
            return 'assets/images/product/kaedc.png';
        } else if(strpos(strtolower($product), 'aedc') !== false) {
            return 'assets/images/product/aedc.png';
        } else if(strpos(strtolower($product), 'ekedc') !== false) {
            return 'assets/images/product/ekedc.jpg';
        } else if(strpos(strtolower($product), 'ikedc') !== false) {
            return 'assets/images/product/ikedc.jpg';
        } else if(strpos(strtolower($product), 'kedc') !== false) {
            return 'assets/images/product/kedco.jpg';
        } else if(strpos(strtolower($product), 'eedc') !== false) {
            return 'assets/images/product/eedc.png';
        } else if(strpos(strtolower($product), 'jedc') !== false) {
            return 'assets/images/product/jedc.jpg';
        } else {

        }
    }

    public function JSAlert($message, $redirectUrl = "") { ?>
        <script>
            alert("<?php echo addslashes($message); ?>");
            <?php if (!empty($redirectUrl)) : ?>
                window.location.href = "<?php echo $redirectUrl; ?>";
            <?php endif; ?>
        </script>
    <?php }

    public function generateSlug(string $data): string {
        // Convert to lowercase
        $string = strtolower($data);

        // Replace spaces and hyphens with underscores
        $string = preg_replace('/[\s\-]+/', '_', $string);

        // Remove multiple underscores (just in case)
        $string = preg_replace('/_+/', '_', $string);

        // Trim leading/trailing underscores
        return trim($string, '_');
    }

    function clean_irregular_spaces($string) {
        // Trim overall string
        $string = trim($string);

        // Remove spaces around hyphens and underscores
        $string = preg_replace('/\s*([-_])\s*/', '$1', $string);

        // Replace multiple spaces with a single space
        $string = preg_replace('/\s+/', ' ', $string);

        return $string;
    }
    
}