<?php
/**
 * CodeIgniter Boot File
 * Modified to use ExpressionEngine's boot system
 */

 define('SELF', basename(__FILE__));
 define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
 define('SYSPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/kodhe/framework' . DIRECTORY_SEPARATOR);


// Define the application path
define('APPPATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

// Define view path
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

define('STORAGEPATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);

// Environment (development, testing, production)
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// Error reporting
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

// Load the boot file
require_once FCPATH.'../bootstrap/app.php';
