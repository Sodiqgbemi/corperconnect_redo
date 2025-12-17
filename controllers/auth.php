<?php

require_once __DIR__ . '/../includes/config.php';

use Helpers\ErrorResponse;
use Includes\ClientLang;
use Includes\EmailSender;
use Includes\Security\CSRF;
use Includes\Security\Validator;
use Helpers\OtpTokens;



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
        
        $user_id = $userData['user_id'];
        var_export( $userData['user_id']);
        exit;
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





