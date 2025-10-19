<?php
/**
 * Authentication Controller
 * Handles login/logout for admin and client users
 */

namespace HostView\Controllers;

class AuthController extends BaseController {
    
    /**
     * Show admin login form
     */
    public function adminLogin() {
        if ($this->isLoggedIn() && $this->getUserRole() === 'admin') {
            $this->redirect('/admin');
        }
        
        $data = [
            'title' => 'Admin Login - ' . APP_NAME,
            'error' => $_SESSION['login_error'] ?? null,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        unset($_SESSION['login_error']);
        
        $this->render('auth/admin-login', $data);
    }
    
    /**
     * Process admin login
     */
    public function processAdminLogin() {
        try {
            $this->validateCsrfToken();
            
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                throw new \Exception('Username and password are required');
            }
            
            // Simple admin authentication (replace with database check)
            if ($username === 'admin' && $password === 'Admin123!') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_role'] = 'admin';
                $_SESSION['user_name'] = 'Administrator';
                $_SESSION['login_time'] = time();
                
                $this->redirect('/admin');
            } else {
                throw new \Exception('Invalid username or password');
            }
            
        } catch (\Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
            $this->redirect('/admin/login');
        }
    }
    
    /**
     * Show client login form
     */
    public function clientLogin() {
        if ($this->isLoggedIn() && $this->getUserRole() === 'client') {
            $this->redirect('/client');
        }
        
        $data = [
            'title' => 'Client Login - ' . APP_NAME,
            'error' => $_SESSION['login_error'] ?? null,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        unset($_SESSION['login_error']);
        
        $this->render('auth/client-login', $data);
    }
    
    /**
     * Process client login
     */
    public function processClientLogin() {
        try {
            $this->validateCsrfToken();
            
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                throw new \Exception('Username and password are required');
            }
            
            // Simple client authentication (replace with FOSSBilling integration)
            if ($username === 'demo' && $password === 'Demo123!') {
                $_SESSION['user_id'] = 100;
                $_SESSION['user_role'] = 'client';
                $_SESSION['user_name'] = 'Demo Client';
                $_SESSION['login_time'] = time();
                
                $this->redirect('/client');
            } else {
                throw new \Exception('Invalid username or password');
            }
            
        } catch (\Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
            $this->redirect('/client/login');
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        $role = $this->getUserRole();
        
        session_destroy();
        session_start();
        
        if ($role === 'admin') {
            $this->redirect('/admin/login');
        } else {
            $this->redirect('/client/login');
        }
    }
}