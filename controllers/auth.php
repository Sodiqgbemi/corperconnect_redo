<?php

require_once __DIR__ . '/../includes/config.php';

use Helpers\ErrorResponse;
use Includes\ClientLang;
use Includes\EmailSender;
use Includes\Security\CSRF;
use Includes\Security\Validator;


// Get and sanitize input data
$postData = array_merge(
    (filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? []),
    (filter_input_array(INPUT_GET, FILTER_DEFAULT) ?? [])
);

$errors = [];

// $email_send_instance = new EmailSender();
// $result = $email_send_instance->send("sodiqgbemishola4@gmail.com", "Error from Copperconnect", 'test it');

if(isset($postData['corper_signup'])){
        try{
        $_SESSION['formInput'] = $postData;
        $csrfToken = $postData['csrf_token'] ?? '';
        $errors = []; // Initialize an empty array to collect all errors

        if(!CSRF::validateCsrfToken($csrfToken)) {
            $errors[] = 'CSRF token validation failed or token expired! Please re-submit your data';
        }

        $myFilters = [
            'first_name' => [
                'validation' =>  'required',
                'sanitization' => 'string|trim|lowercase',
            ],
            'last_name' => [
                'validation' =>  'required',
                'sanitization' => 'string|trim|lowercase',
            ],
            'email' => [
                'validation' =>  'required|email',
                'sanitization' => 'string|trim|lowercase',
            ],
            'password' => [
                'validation' =>  'required|minlen:5',
                'sanitization' => 'string',
            ],

            'password2'=> [
                'validation' => 'required|minlen:5|same:password',  // PASSWORDS MUST MATCH
                'sanitization' => 'string',
            ]
        ];

        $validator = new Validator ($myFilters);
        $sanitizedData = $validator->run($postData);
        if(!$sanitizedData) {
            $errors = array_merge($errors, $validator->getValidationErrors());
        }

        $first_name = $sanitizedData['first_name'];
        $last_name = $sanitizedData['last_name'];
        $password = $sanitizedData['password'];
        $password2 = $sanitizedData['password2'];
        $emailaddress = $sanitizedData['email'];

        // Check if password is too short
        if (strlen($password) < 5) {
            $errors[] = ClientLang::PASS_LEN_5;
        }

        if ($password2 !== $password ){
            $errors[] = ClientLang::NEW_EQUAL_OLD_PASSWORD;
        }

        if (!filter_var($emailaddress, FILTER_VALIDATE_EMAIL)) {
            $errors[] = ClientLang::INVALID_EMAIL;
        }

        if (!empty($user_instance->getUserByEmail($emailaddress))) {
            $errors[] = ClientLang::EMAIL_EXIST;
        }

        // Handle errors or proceed
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }

        $create_user = $user_instance->createUser([
            'users_fname' => $first_name,
            'users_lname' => $last_name,
            'users_email' => $emailaddress,
            'users_password' => $user_instance->hashPassword($password), 
        ]);
        
        if($create_user){      
             $_SESSION['successMessage'] = ClientLang::REGISTER_SUCCESS;
                header("location: ".AUTH_URL."login"); 
                exit;

        } else {
             $_SESSION['errorMessage'] = ClientLang::REGISTER_FAILED;
            header("location: ".REFERER);
            exit;
        }

        } catch (PDOException | Throwable $e ) {
           $_SESSION['errorMessage'] = ErrorResponse::formatResponse($e);
           header("location: ".REFERER);
           exit;
        }
}

if (isset($postData["corper_login"])) {
    try {

        $_SESSION['formInput'] = $postData;
        $csrfToken = $postData['csrf_token'] ?? '';

        // CSRF Token Validation
        if (!CSRF::validateCsrfToken($csrfToken)) {
            $errors[] = 'CSRF token validation failed or token expired! Please re-submit your data';
        }

        $myFilters = [
            'email' => [
                'validation' => 'required|email',
                'sanitization' => 'string|trim|lowercase',
            ],
            'password' => [
                'validation' => 'required|minlen:5',
                'sanitization' => 'string',
            ],
        ];

       
        $validator = new Validator($myFilters);
        $sanitizedData = $validator->run($postData);
        if (!$sanitizedData) {
            $errors = array_merge($errors, $validator->getValidationErrors());
        }


        $userEmail = $sanitizedData["email"];
        $password = $sanitizedData['password'];

        // Check if password is too short
        if (strlen($password) < 5) {
            $errors[] = ClientLang::PASS_LEN_5;
        }

        $userData = $user_instance->getUserByEmail($userEmail);
        if ($userData === false) {
            $errors[] = ClientLang::INVALID_CREDENTIALS;
        }
        
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }
        
        
        $user_id = $userData['user_id'];
        if (password_verify($password, $userData['users_password'])) {

          $_SESSION['userid'] = $user_id;;
            $user_instance->clearFormSessions();
            header("location: " . USER_ACCESS_DIR . "dashboard");
            exit;
            
        } else {
            $_SESSION['errorMessage'] = ClientLang::INVALID_CREDENTIALS;
            header("location: " . REFERER);
            exit;
        }

    } catch (Throwable $e) {
        $_SESSION['errorMessage'] = ErrorResponse::formatResponse($e);
        header("location: " . REFERER);
        exit;
    }
}

if (isset($postData['initiate_password_reset'])) {
    try {
        $_SESSION['formInput'] = $postData;
        $csrfToken = $postData['csrf_token'] ?? '';
        $errors = []; // Initialize an empty array to collect all errors

        // CSRF Token Validation
        if (!CSRF::validateCsrfToken($csrfToken)) {
            $errors[] = 'CSRF token validation failed or token expired! Please re-submit your data';
        }

        $myFilters = [
            'user_detail' => [
                'validations' => 'required',
            ],
        ];

        $validator = new Validator($myFilters);
        $sanitizedData = $validator->run($postData);
        if (!$sanitizedData) {
            $errors = array_merge($errors, $validator->getValidationErrors());
        }
                
        // Handle errors or proceed
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }
        
        $user_detail = strtolower($sanitizedData['user_detail']);
        $user = $user_instance->getUser($user_detail);

        if (!$user) {
            $_SESSION['errorMessage'] = ClientLang::USER_NOT_FOUND;
            header("location: " . REFERER);
            exit;
        }

        $userId = $user['id'];
        $rateLimitCheck = RateLimit::checkRateLimit('initiate_password_' . $userId, 3);

        if (!$rateLimitCheck['status']) {
            $_SESSION['errorMessage'] = $rateLimitCheck['message'];
            header("location: " . REFERER);
            exit;
        }

        $otp_token_instance = new OtpTokens($db);
        $db->beginTransaction();

        $otp_exists = $otp_token_instance->get_otp_token('passwordreset', $userId);

        $isOtpExist = $createOtp = false;
        if ($otp_exists != NULL) {
            if ($otp_exists['status'] == 'new' and $otp_token_instance->checkTimeDuration($otp_exists['created_at'])) {
                var_export($otp_exists);
                $isOtpExist = true;
            } else {
                $otp_token_instance->updateToken($otp_exists['code'], $userId, 'expired');
            }
        }
        
        if (!$isOtpExist) {
            $createOtp = $otp_token_instance->createOtpToken([
                'userid' => $userId,
                'otp_code' => $otp_token_instance->generateOtpCode(),
                'status' => 'new',
                'use_case' => 'passwordreset'
            ]);
        }
        
        if (!$createOtp AND !$isOtpExist) {
            $db->rollBack();
            $_SESSION['errorMessage'] = ClientLang::REQUEST_FAILED;
            header("location: " . REFERER);
            exit;
        }
        
        $_SESSION['reset_password']['user_id'] = $userId;
        $cron_event_instance->createEvent($userId, 'email', 'passwordreset');
        $db->commit();
        $_SESSION['successMessage'] = ClientLang::OTP_SENT_EMAIL;
        header("location: " . AUTH_URL.'verifyotp');
        exit;

    } catch (Throwable $e) {
        $db->rollBack();
        $_SESSION['errorMessage'] = ErrorResponse::formatResponse($e);
        header("location: " . REFERER);
        exit;
    }
}

if (isset($postData['change_password'])) {
    try {
        
        $_SESSION['formInput'] = $postData;
        $csrfToken = $postData['csrf_token'] ?? '';
        $errors = []; // Initialize an empty array to collect all errors

        // CSRF Token Validation
        if (!CSRF::validateCsrfToken($csrfToken)) {
            $errors[] = 'CSRF token validation failed or token expired! Please re-submit your data';
        }

        $myFilters = [
            'new_password' => [
                'sanitization' => 'string',
                'validations' => 'required',
            ],
            'confirm_password' => [
                'sanitization' => 'string',
                'validations' => 'required',
            ],
        ];

        $validator = new Validator($myFilters);
        $sanitizedData = $validator->run($postData);
        if (!$sanitizedData) {
            $errors = array_merge($errors, $validator->getValidationErrors());
        }

        // Check if password is too short
        if ($postData['new_password'] != $postData['confirm_password']) {
            $errors[] = ClientLang::PASSWORD_MISMATCH;
        }

        // Check if password is too short
        if (strlen($postData['new_password']) < 5) {
            $errors[] = ClientLang::PASS_LEN_5;
        }

        $userId = $_SESSION['reset_password']['user_id'] ?? "";
        $otpCode = $_SESSION['reset_password']['otp_code'] ?? "";

        if (empty($userId) OR $user_instance->getUserById($userId) === false) {
            $errors[] = ClientLang::USER_NOT_FOUND;
        }

        if (empty($otpCode)) {
            $errors[] = ClientLang::REQUEST_FAILED;
        }
                
        // Handle errors or proceed
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }

        $userData = ['password' => $user_instance->hashPassword($sanitizedData['new_password'])];
        $resetPassword = $user_instance->updateUser($userData, $userId);

        if ($resetPassword) {
            unset($_SESSION['reset_password']);
            (new OtpTokens($db))->updateToken($otpCode, $userId, 'used');
            $_SESSION['successMessage'] = ClientLang::PASSWORD_CHANGED_SUCCESS;
            header("location: " . AUTH_URL.'login');
            exit;
        } else {
            $_SESSION['errorMessage'] = ClientLang::PASS_RESET_ERROR;
            header("location: " . REFERER);
            exit;
        }

    } catch (Throwable|Exception $e) {
        $_SESSION['errorMessage'] = ErrorResponse::formatResponse($e);
        header("location: " . REFERER);
        exit;
    }
}


