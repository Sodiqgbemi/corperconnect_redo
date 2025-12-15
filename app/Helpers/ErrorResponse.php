<?php

namespace Helpers;

use Model\Utility;

class ErrorResponse
{
    public function __construct() {}

    public function __destruct() {}

    public static function formatResponse($exemption)
    {
        $errorMessage = $exemption->getMessage();

        $lowerErrorMsg = str_replace(["'", "`"], "", strtolower($errorMessage));
        if (str_contains($lowerErrorMsg, "duplicate")) {
            $outputError = $errorMessage;
            self::logger($exemption, "db_error");
        } else if (
            str_contains($lowerErrorMsg, "unknown column") or str_contains($lowerErrorMsg, "doesnt exist") or str_contains($lowerErrorMsg, "doesnt have a default value")
            or str_contains($lowerErrorMsg, "cannot be null") or str_contains($lowerErrorMsg, "active transaction")
        ) {
            $outputError = "There was an issue processing your request. Please try again later. If the problem persists, contact support.";
            self::logger($exemption, "fatal_error");
        } else if (str_contains($lowerErrorMsg, "could not resolve host")) {
            $outputError = "Error communicating to provider. Please contact Administrator.";
            self::logger($exemption, "fatal_error");
        } else if (str_contains($lowerErrorMsg, "return value must be of")) {
            $outputError = "System glitch. Please try again later. If the problem persists, contact support.";
            self::logger($exemption, "fatal_error");
        } else {
            $outputError = "An error occurred. Please contact support.";
            self::logger($exemption, "common_error");
        }

        http_response_code(400);
        return $outputError;
    }

    private static function logger($exemption, $type = "common_error")
    {
        if ($type == "db_error") {
            Utility::log_txt('database_errors', $exemption, 'logs');
        } else if ($type == "fatal_error") {
            Utility::log_txt('fatal_errors', $exemption, 'logs');
        } else {
            Utility::log_txt('common_errors', $exemption, 'logs');
        }
        
    }
}
