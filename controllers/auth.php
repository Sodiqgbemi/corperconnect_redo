<?php

require_once __DIR__ . '/../includes/config.php';

use Helpers\ErrorResponse;
use Includes\ClientLang;
use Includes\Security\CSRF;
use Model\User;
use Includes\Security\Validator;

$user_instance = new User($db); 

// Get and sanitize input data
$postData = array_merge(
    (filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? []),
    (filter_input_array(INPUT_GET, FILTER_DEFAULT) ?? [])
);

$errors = [];

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
                    'validation' => 'required|minlen:5',  // PASSWORDS MUST MATCH
                    'sanitization' => 'string',
                ]
            ];

            $validator = new Validator ($myFilters);
            $sanitizedData = $validator->run($postData);
            if(!$sanitizedData) {
                $errors = array_merge($errors, $validator->getValidationErrors());
            }

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
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email_address' => $emailaddress,
            'password' => $user_instance->hashPassword($password), 
        ]);
        if($create_user){      
             $_SESSION['successMessage'] = ClientLang::REGISTER_SUCCESS;
                header("location: " . REFERER); 
                exit;

        } else {
             $_SESSION['errorMessage'] = ClientLang::REGISTER_FAILED;
            header("location: ".AUTH_URL."login");
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

        if (!empty($errors)) {
            echo "Error found";
            exit; 
        }

        $userEmail = $sanitizedData["email"];

        $getUser = $user_instance->getUserByEmail($userEmail);

        if (!empty($getUser)) {
            $_SESSION['errorMessage'] = ClientLang::USER_NOT_FOUND;
            header("location: ".AUTH_URL."login");
            exit;
        }


        var_export($getUser);
        die;
        
    } catch (Throwable $e) {
        echo ErrorResponse::formatResponse($e);
    }
}


