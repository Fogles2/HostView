<?php
/**
 * HostView - PHP Hosting Billing Dashboard
 * Main Application Entry Point
 * 
 * @author Michael McDaniel <admin@turnpage.io>
 * @copyright 2025 Turnpage Networks
 * @license MIT
 */

// Start session and set error reporting
session_start();
error_reporting(E_ALL & ~E_NOTICE);

// Include autoloader
require_once '../vendor/autoload.php';

// Load environment variables
if (file_exists('../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();
}

// Application configuration
define('APP_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', APP_PATH . '/storage');

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_DATABASE'] ?? 'hostview');
define('DB_USER', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? '');

// FOSSBilling API configuration
define('FOSSBILLING_URL', $_ENV['FOSSBILLING_URL'] ?? 'https://billing.turnpage.io');
define('FOSSBILLING_API_KEY', $_ENV['FOSSBILLING_API_KEY'] ?? 'trGUFHOHLOt19wqDLEkhORT4iD8JNTuR');
define('FOSSBILLING_USERNAME', $_ENV['FOSSBILLING_USERNAME'] ?? 'admin');

// Application settings
define('APP_NAME', $_ENV['APP_NAME'] ?? 'HostView');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Include router
require_once '../src/Router.php';

// Initialize router
$router = new \HostView\Router();

// Define routes
$router->get('/', 'HomeController@index');
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/login', 'AuthController@adminLogin');
$router->post('/admin/login', 'AuthController@processAdminLogin');
$router->get('/admin/logout', 'AuthController@logout');
$router->get('/admin/clients', 'AdminController@clients');
$router->get('/admin/invoices', 'AdminController@invoices');
$router->get('/admin/services', 'AdminController@services');
$router->get('/admin/servers', 'AdminController@servers');
$router->get('/admin/settings', 'AdminController@settings');

$router->get('/client', 'ClientController@dashboard');
$router->get('/client/login', 'AuthController@clientLogin');
$router->post('/client/login', 'AuthController@processClientLogin');
$router->get('/client/services', 'ClientController@services');
$router->get('/client/invoices', 'ClientController@invoices');
$router->get('/client/support', 'ClientController@support');

// API routes
$router->get('/api/dashboard/stats', 'ApiController@dashboardStats');
$router->get('/api/clients', 'ApiController@clients');
$router->get('/api/invoices', 'ApiController@invoices');
$router->get('/api/fossbilling/test', 'ApiController@testFossbilling');

// Health check
$router->get('/health', 'ApiController@health');

// Handle the request
try {
    $router->dispatch();
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo '<h1>Application Error</h1>';
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
    
    // Log the error
    error_log('HostView Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}