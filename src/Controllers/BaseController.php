<?php
/**
 * Base Controller
 * Provides common functionality for all controllers
 */

namespace HostView\Controllers;

class BaseController {
    protected $viewsPath;
    
    public function __construct() {
        $this->viewsPath = APP_PATH . '/templates';
    }
    
    /**
     * Render a view template
     */
    protected function render($view, $data = []) {
        extract($data);
        
        $viewFile = $this->viewsPath . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View file not found: {$viewFile}");
        }
    }
    
    /**
     * Redirect to another URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user role
     */
    protected function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Get current user ID
     */
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken() {
        $token = $_POST['_token'] ?? $_GET['_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            throw new \Exception('CSRF token validation failed');
        }
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}