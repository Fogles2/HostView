<?php
/**
 * API Controller
 * Handles API endpoints for AJAX requests
 */

namespace HostView\Controllers;

use HostView\API\FOSSBillingClient;

class ApiController extends BaseController {
    private $fossBilling;
    
    public function __construct() {
        parent::__construct();
        $this->fossBilling = new FOSSBillingClient();
    }
    
    /**
     * Get dashboard statistics
     */
    public function dashboardStats() {
        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $stats = [
            'clients' => 0,
            'services' => 0,
            'revenue' => 0,
            'invoices' => 0
        ];
        
        // Get real data from FOSSBilling
        $clientsResult = $this->fossBilling->getClients(1, 1);
        if ($clientsResult['success']) {
            $stats['clients'] = $clientsResult['data']['total'] ?? 0;
        }
        
        $ordersResult = $this->fossBilling->getOrders(1, 1);
        if ($ordersResult['success']) {
            $stats['services'] = $ordersResult['data']['total'] ?? 0;
        }
        
        $invoicesResult = $this->fossBilling->getInvoices(1, 100);
        if ($invoicesResult['success'] && isset($invoicesResult['data']['list'])) {
            $stats['invoices'] = $invoicesResult['data']['total'] ?? 0;
            
            // Calculate revenue from paid invoices
            foreach ($invoicesResult['data']['list'] as $invoice) {
                if (($invoice['status'] ?? '') === 'paid') {
                    $stats['revenue'] += floatval($invoice['total'] ?? 0);
                }
            }
        }
        
        $this->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => time()
        ]);
    }
    
    /**
     * Get clients list
     */
    public function clients() {
        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $page = intval($_GET['page'] ?? 1);
        $result = $this->fossBilling->getClients($page, 20);
        
        $this->json($result);
    }
    
    /**
     * Get invoices list
     */
    public function invoices() {
        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $page = intval($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? '';
        
        $result = $this->fossBilling->getInvoices($page, 20, $status);
        
        $this->json($result);
    }
    
    /**
     * Test FOSSBilling connection
     */
    public function testFossbilling() {
        if (!$this->isLoggedIn() || $this->getUserRole() !== 'admin') {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $result = $this->fossBilling->testConnection();
        $this->json($result);
    }
    
    /**
     * Health check endpoint
     */
    public function health() {
        $health = [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => '1.0.0',
            'checks' => [
                'database' => 'unknown',
                'fossbilling' => 'unknown'
            ]
        ];
        
        // Test FOSSBilling connection
        $fossTest = $this->fossBilling->testConnection();
        $health['checks']['fossbilling'] = $fossTest['success'] ? 'healthy' : 'error';
        
        if (!$fossTest['success']) {
            $health['status'] = 'degraded';
        }
        
        $this->json($health);
    }
}