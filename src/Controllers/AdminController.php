<?php
/**
 * Admin Controller
 * Handles admin dashboard and management functions
 */

namespace HostView\Controllers;

use HostView\API\FOSSBillingClient;

class AdminController extends BaseController {
    private $fossBilling;
    
    public function __construct() {
        parent::__construct();
        $this->fossBilling = new FOSSBillingClient();
        
        // Check if user is logged in as admin
        if (!$this->isLoggedIn() || $this->getUserRole() !== 'admin') {
            $this->redirect('/admin/login');
        }
    }
    
    /**
     * Admin dashboard with real FOSSBilling data
     */
    public function dashboard() {
        $data = [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'page' => 'dashboard',
            'stats' => $this->getDashboardStats(),
            'recent_clients' => $this->getRecentClients(),
            'recent_invoices' => $this->getRecentInvoices(),
            'server_status' => $this->getServerStatus()
        ];
        
        $this->render('admin/dashboard', $data);
    }
    
    /**
     * Get dashboard statistics from FOSSBilling
     */
    private function getDashboardStats() {
        $stats = [
            'total_clients' => 0,
            'active_services' => 0,
            'total_revenue' => 0.00,
            'pending_invoices' => 0,
            'overdue_invoices' => 0,
            'monthly_revenue' => 0.00,
            'new_clients_month' => 0
        ];
        
        // Get client count
        $clientsResult = $this->fossBilling->getClients(1, 1);
        if ($clientsResult['success'] && isset($clientsResult['data']['total'])) {
            $stats['total_clients'] = $clientsResult['data']['total'];
        }
        
        // Get orders/services count
        $ordersResult = $this->fossBilling->getOrders(1, 1);
        if ($ordersResult['success'] && isset($ordersResult['data']['total'])) {
            $stats['active_services'] = $ordersResult['data']['total'];
        }
        
        // Get invoice statistics
        $invoicesResult = $this->fossBilling->getInvoices(1, 100); // Get more for calculations
        if ($invoicesResult['success'] && isset($invoicesResult['data']['list'])) {
            $invoices = $invoicesResult['data']['list'];
            
            foreach ($invoices as $invoice) {
                $amount = floatval($invoice['total'] ?? 0);
                $status = strtolower($invoice['status'] ?? '');
                
                if ($status === 'paid') {
                    $stats['total_revenue'] += $amount;
                    
                    // Check if invoice is from this month
                    $invoiceDate = strtotime($invoice['created_at'] ?? '');
                    if ($invoiceDate && date('Y-m', $invoiceDate) === date('Y-m')) {
                        $stats['monthly_revenue'] += $amount;
                    }
                } elseif ($status === 'unpaid') {
                    $stats['pending_invoices']++;
                    
                    // Check if overdue
                    $dueDate = strtotime($invoice['due_at'] ?? '');
                    if ($dueDate && $dueDate < time()) {
                        $stats['overdue_invoices']++;
                    }
                }
            }
        }
        
        // Calculate new clients this month
        $allClientsResult = $this->fossBilling->getClients(1, 100);
        if ($allClientsResult['success'] && isset($allClientsResult['data']['list'])) {
            $clients = $allClientsResult['data']['list'];
            
            foreach ($clients as $client) {
                $createdDate = strtotime($client['created_at'] ?? '');
                if ($createdDate && date('Y-m', $createdDate) === date('Y-m')) {
                    $stats['new_clients_month']++;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Get recent clients from FOSSBilling
     */
    private function getRecentClients() {
        $result = $this->fossBilling->getClients(1, 5);
        
        if ($result['success'] && isset($result['data']['list'])) {
            return array_map(function($client) {
                return [
                    'id' => $client['id'],
                    'name' => ($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''),
                    'email' => $client['email'] ?? '',
                    'status' => $client['status'] ?? 'active',
                    'created_at' => $client['created_at'] ?? ''
                ];
            }, $result['data']['list']);
        }
        
        return [];
    }
    
    /**
     * Get recent invoices from FOSSBilling
     */
    private function getRecentInvoices() {
        $result = $this->fossBilling->getInvoices(1, 5);
        
        if ($result['success'] && isset($result['data']['list'])) {
            return array_map(function($invoice) {
                return [
                    'id' => $invoice['id'],
                    'number' => $invoice['serie_nr'] ?? $invoice['id'],
                    'client_name' => $invoice['buyer_first_name'] . ' ' . $invoice['buyer_last_name'],
                    'amount' => floatval($invoice['total'] ?? 0),
                    'status' => $invoice['status'] ?? 'unpaid',
                    'due_date' => $invoice['due_at'] ?? '',
                    'created_at' => $invoice['created_at'] ?? ''
                ];
            }, $result['data']['list']);
        }
        
        return [];
    }
    
    /**
     * Get server status (placeholder for Plesk integration)
     */
    private function getServerStatus() {
        return [
            [
                'name' => 'Web Server 1',
                'status' => 'online',
                'cpu' => 45,
                'memory' => 68,
                'disk' => 34
            ],
            [
                'name' => 'Mail Server',
                'status' => 'online',
                'cpu' => 25,
                'memory' => 40,
                'disk' => 20
            ]
        ];
    }
    
    /**
     * Clients management page
     */
    public function clients() {
        $page = intval($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        
        $result = $this->fossBilling->getClients($page, 20);
        
        $data = [
            'title' => 'Client Management - ' . APP_NAME,
            'page' => 'clients',
            'clients' => $result['success'] ? ($result['data']['list'] ?? []) : [],
            'pagination' => $result['success'] ? $result['data'] : [],
            'search' => $search,
            'api_error' => !$result['success'] ? $result['error'] : null
        ];
        
        $this->render('admin/clients', $data);
    }
    
    /**
     * Invoices management page
     */
    public function invoices() {
        $page = intval($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        
        $result = $this->fossBilling->getInvoices($page, 20, $status);
        
        $data = [
            'title' => 'Invoice Management - ' . APP_NAME,
            'page' => 'invoices',
            'invoices' => $result['success'] ? ($result['data']['list'] ?? []) : [],
            'pagination' => $result['success'] ? $result['data'] : [],
            'status_filter' => $status,
            'api_error' => !$result['success'] ? $result['error'] : null
        ];
        
        $this->render('admin/invoices', $data);
    }
    
    /**
     * Services management page
     */
    public function services() {
        $page = intval($_GET['page'] ?? 1);
        
        $ordersResult = $this->fossBilling->getOrders($page, 20);
        $productsResult = $this->fossBilling->getProducts();
        
        $data = [
            'title' => 'Service Management - ' . APP_NAME,
            'page' => 'services',
            'orders' => $ordersResult['success'] ? ($ordersResult['data']['list'] ?? []) : [],
            'products' => $productsResult['success'] ? ($productsResult['data']['list'] ?? []) : [],
            'pagination' => $ordersResult['success'] ? $ordersResult['data'] : [],
            'api_error' => !$ordersResult['success'] ? $ordersResult['error'] : null
        ];
        
        $this->render('admin/services', $data);
    }
    
    /**
     * Servers monitoring page
     */
    public function servers() {
        $data = [
            'title' => 'Server Monitoring - ' . APP_NAME,
            'page' => 'servers',
            'servers' => $this->getServerStatus()
        ];
        
        $this->render('admin/servers', $data);
    }
    
    /**
     * Settings page
     */
    public function settings() {
        $data = [
            'title' => 'System Settings - ' . APP_NAME,
            'page' => 'settings',
            'fossbilling_url' => FOSSBILLING_URL,
            'api_test' => $this->fossBilling->testConnection()
        ];
        
        $this->render('admin/settings', $data);
    }
}