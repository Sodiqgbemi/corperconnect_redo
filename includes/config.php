<?php

namespace Includes;
// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
// error_reporting(0);

use PDO;
use Model\User;
use PDOException;
use Model\Utility;
use Includes\Database;
use Includes\envLoader;

// Now you can access environment variables using getenv() or $_ENV
ini_set("display_errors", envLoader::get_key('APP_DEBUG') ? 1 : 0);
ini_set("display_startup_errors", envLoader::get_key('APP_DEBUG') ? 1 : 0);

ob_start();
session_start();
if(session_id() === NULL) {
    session_set_cookie_params(3600);
}

// Use environment variables for configuration
define('BASE_PATH', envLoader::get_key('BASE_PATH') ?: '/');
define('DB_NAME', envLoader::get_key('DB_NAME'));
define('DB_USER', envLoader::get_key('DB_USER'));
define('DB_PASSWORD', envLoader::get_key('DB_PASSWORD'));
define('DB_HOST', envLoader::get_key('DB_HOST'));

// Sever constants
define('SERVER', $_SERVER['SERVER_NAME']);
define('APP_NAME', envLoader::get_key('APP_NAME'));
define('PAGE', pathinfo(basename($_SERVER['PHP_SELF']), PATHINFO_FILENAME));
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('SCHEME', $_SERVER['REQUEST_SCHEME']);
define('PORT', $_SERVER['SERVER_PORT']);
define('REQUEST_URI', $_SERVER['REQUEST_URI']);
define('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
define('REFERER', $_SERVER['HTTP_REFERER'] ?? '');

// Application constants
define('BASE_URL', SCHEME . '://' . SERVER . BASE_PATH);
define('AUTH_URL', BASE_URL.'auth/');
define('ADMIN_AUTH_URL', SCHEME . '://' . SERVER . BASE_PATH.'panel/');
define('USER_ACCESS_DIR', BASE_URL . 'v/');
define('ADMIN_ACCESS_DIR', BASE_URL . 'panel/');
define('USER_MODAL_DIR', ROOT . BASE_PATH . 'v/modals/');
define('TEMPLATE_DIR', ROOT . BASE_PATH . 'templates/');
define('EMAIL_TEMPLATE_DIR', TEMPLATE_DIR . 'emails/');
define('CONTROLLER_URL', BASE_URL.'controllers/');
define('USER_CONTROLLER_URL', BASE_URL.'controllers/user/');
define('ADMIN_CONTROLLER_URL', BASE_URL.'controllers/admin/');
define('COMPONENT_DIR', ROOT . BASE_PATH . 'components/');
define('GUEST_COMPONENT_DIR', ROOT . BASE_PATH . 'components/guest/');
define('USER_COMPONENT_DIR', ROOT . BASE_PATH . 'components/user/');
define('ADMIN_COMPONENT_DIR', ROOT . BASE_PATH . 'components/admin/');
define('USER_MODAL_COMPONENT_DIR', ROOT . BASE_PATH . 'components/user/modals/');
define('ADMIN_MODAL_COMPONENT_DIR', ROOT . BASE_PATH . 'components/admin/modals/');
define('ENCRYPT_CODE', '$p0RtOd$$1');

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

$db = new Database($pdo);
$utility_instance = new Utility($db);
$user_instance = new User($db);

$pageTitle = 'CorperConnect';

define("PAGE_TITLE", is_null($pageTitle) ? APP_NAME : APP_NAME . ' | ' . $pageTitle);

// Other application logic here...
date_default_timezone_set(envLoader::get_key('TIMEZONE'));

// $userId = $utility_instance->get_current_user();
$userData = $adminData = [];
$userId = '';

if (!empty($userId)) {
    $userData = $user_instance->getUserById($userId);
}

if (isset($_SESSION['adminid'])) {
    $adminData = $admin_instance->getAdminById($utility_instance->get_current_user('admin'));
}