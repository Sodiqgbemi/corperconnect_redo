<?php
namespace Includes;

use ErrorException;

class envLoader
{
    public static function pathLoader()
    {
        $envPath = self::getRootFolder() . ".env";
        if (!is_file($envPath) or !is_readable($envPath)) {
            throw new ErrorException("Error setting up application. Configuration file not found");
            exit;
        }
        return $envPath;
    }

    public static function get_key($envKey)
    {
        $envInfo = parse_ini_file(self::pathLoader());
        $envDetail = isset($envInfo[$envKey]) ? $envInfo[$envKey] : NULL;
        return $envDetail;
    }


    private static function getRootFolder() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $file_name = '';
            $absolute_path = $_SERVER["DOCUMENT_ROOT"];
            $root_folder = str_replace($file_name, '', $absolute_path);
            $root_folder = $root_folder . "/";
        } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR') { 
            $root_folder = rtrim($_SERVER["DOCUMENT_ROOT"], '/') . '/';
        } else {
            $current_file_path = __FILE__;
            $file_name = basename($current_file_path);
            $absolute_path = $_SERVER["DOCUMENT_ROOT"];
            $root_folder = str_replace($file_name, '', $current_file_path);
            $root_folder = $root_folder . "/";
        }
        return $root_folder;
    }
}