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

