<?php

require_once __DIR__ . '/../includes/config.php';

use Helpers\ErrorResponse;
use Includes\ClientLang;
use Includes\EmailSender;
use Includes\Security\CSRF;
use Includes\Security\Validator;
use Model\ResetRequest;


// Get and sanitize input data
$postData = array_merge(
    (filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? []),
    (filter_input_array(INPUT_GET, FILTER_DEFAULT) ?? [])
);

$errors = [];

$resetrequest_instance = new ResetRequest($db);

$email_send_instance = new EmailSender();
// $result = $email_send_instance->send("sodiqgbemishola4@gmail.com", "Error from Copperconnect", 'test it');

if(isset($postData['corper_signup'])){
        try {
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

            if (!empty($user_instance->getUser($emailaddress))) {
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

        $userData = $user_instance->getUser($userEmail);
        if (empty($userData)) {
            $errors[] = ClientLang::INVALID_CREDENTIALS;
        }
        
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }
        
        $user_id = $userData['user_id'];
        $getUserPassword = $user_instance->getUserPassword($userEmail);
        if (password_verify($password, $getUserPassword)) {

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

        if (empty($userId) OR $user_instance->getUser($userId) === false) {
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

if (isset($postData['request_reset_link'])) {
    try {

        $_SESSION['formInput'] = $postData;
        $csrfToken = $postData['csrf_token'] ?? '';
        $errors = []; // Initialize an empty array to collect all errors

        if(!CSRF::validateCsrfToken($csrfToken)) {
            $errors[] = 'CSRF token validation failed or token expired! Please re-submit your data';
        }

        $myFilters = [
            'email' => [
                'validation' =>  'required|email',
                'sanitization' => 'string|trim|lowercase',
            ]
        ];

        $validator = new Validator ($myFilters);
        $sanitizedData = $validator->run($postData);
        if(!$sanitizedData) {
            $errors = array_merge($errors, $validator->getValidationErrors());
        }

        if (!empty($errors)) {
            $_SESSION['errorMessage'] = $errors;
            header("location: " . REFERER);
            exit;
        }

        $userEmail = $sanitizedData['email'];

        $getUser = $user_instance->getUser($userEmail);

        if (empty($getUser)) {
            $_SESSION['errorMessage'] = ClientLang::USER_NOT_FOUND;
            header("location: " . REFERER);
            exit;
        }

        $userId = $getUser['user_id'];
        $getUserLink = $resetrequest_instance->getUserRequestLink($userId);

        $emailSubject = "Reset Link from ".APP_NAME;

        if (!empty($getUserLink)) {
            $isLinkValid = $resetrequest_instance->checkTimeDuration($getUserLink['created_at'], 10);

            if (!$isLinkValid) {
                $resetLinkCode = $utility_instance->randID('alphanumeric', 64);
                $resetrequest_instance->delete_request($getUserLink['request_id']);

                $requestData = [
                    'user_id' => $userId,
                    'request_link' => $resetLinkCode
                ];
                
                $createReset = $resetrequest_instance->create_request($requestData);
                
                if ($createReset) {
                    $resetLink = AUTH_URL.'resetPassword?reset='.$resetLinkCode;

                    $resetMessage = "Dear ".$getUser['users_fname']." You recent made a request to reset your email. <br> Here is a reset link <br><br>

                    <a href='$resetLink'>Click Here</a>

                    <br><br>
                    Alternatively copy this link

                    <br>

                        {$resetLink}
                    <br><br> Thank you";
                    
                    // $email_send_instance->send($getUser['users_email'], $emailSubject, $resetMessage);

                    $_SESSION['successMessage'] = ClientLang::PASS_RESET_SENT;
                    header("location: " . REFERER);
                    exit;
                    
                } else {
                    $_SESSION['errorMessage'] = ClientLang::REQUEST_FAILED;
                    header("location: " . REFERER);
                    exit;
                }

            } else {
                $resetLink = AUTH_URL.'resetPassword?reset='.$resetLinkCode;

                $resetMessage = "Dear ".$getUser['users_fname']." You recent made a request to reset your email. <br> Here is a reset link <br><br>

                <a href='$resetLink'>Click Here</a>

                <br><br>
                Alternatively copy this link

                <br>

                    {$resetLink}
                <br><br> Thank you";
                
                // $email_send_instance->send($getUser['users_email'], $emailSubject, $resetMessage);

                $_SESSION['successMessage'] = ClientLang::PASS_RESET_SENT;
                header("location: " . REFERER);
                exit;
            }
        }
        else {
            $resetLinkCode = $utility_instance->randID('alphanumeric', 64);
           
            $requestData = [
                'user_id' => $userId,
                'request_link' => $resetLinkCode
            ];
            
            $createReset = $resetrequest_instance->create_request($requestData);
            
            if ($createReset) {
                $resetLink = AUTH_URL.'resetPassword?reset='.$resetLinkCode;

                $resetMessage = "Dear ".$getUser['users_fname']." You recent made a request to reset your email. <br> Here is a reset link <br><br>

                <a href='$resetLink'>Click Here</a>

                <br><br>
                Alternatively copy this link

                <br>

                    {$resetLink}
                <br><br> Thank you";
                
                // $email_send_instance->send($getUser['users_email'], $emailSubject, $resetMessage);

                $_SESSION['successMessage'] = ClientLang::PASS_RESET_SENT;
                header("location: " . REFERER);
                exit;
                
            } else {
                $_SESSION['errorMessage'] = ClientLang::REQUEST_FAILED;
                header("location: " . REFERER);
                exit;
            }

        }

    } catch (Throwable|Exception $e) {
        $_SESSION['errorMessage'] = ErrorResponse::formatResponse($e);
        header("location: " . REFERER);
        exit;
    } 
}