<?php
/**
 * FOSSBilling API Client
 * Handles all communication with FOSSBilling instance
 */

namespace HostView\API;

class FOSSBillingClient {
    private $baseUrl;
    private $apiKey;
    private $username;
    private $timeout;
    private $cache = [];
    private $cacheEnabled = true;
    private $cacheTtl = 300; // 5 minutes
    
    public function __construct() {
        $this->baseUrl = rtrim(FOSSBILLING_URL, '/');
        $this->apiKey = FOSSBILLING_API_KEY;
        $this->username = FOSSBILLING_USERNAME;
        $this->timeout = $_ENV['FOSSBILLING_TIMEOUT'] ?? 30;
        $this->cacheTtl = $_ENV['FOSSBILLING_CACHE_TTL'] ?? 300;
    }
    
    /**
     * Make API request to FOSSBilling
     */
    public function makeRequest($endpoint, $params = [], $useCache = true) {
        $cacheKey = md5($endpoint . serialize($params));
        
        // Check cache first
        if ($useCache && $this->cacheEnabled && isset($this->cache[$cacheKey])) {
            $cached = $this->cache[$cacheKey];
            if (time() - $cached['timestamp'] < $this->cacheTtl) {
                return $cached['data'];
            }
        }
        
        $url = $this->baseUrl . $endpoint;
        $auth = base64_encode($this->username . ':' . $this->apiKey);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
                'User-Agent: HostView/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Handle cURL errors
        if ($error) {
            $this->logError('cURL Error: ' . $error);
            return [
                'success' => false,
                'error' => 'Connection failed: ' . $error,
                'retry' => true
            ];
        }
        
        // Handle HTTP errors
        if ($httpCode >= 400) {
            $this->logError('HTTP Error: ' . $httpCode . ' - ' . $response);
            return [
                'success' => false,
                'error' => $this->getHttpErrorMessage($httpCode),
                'retry' => $httpCode >= 500
            ];
        }
        
        // Parse JSON response
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError('JSON Parse Error: ' . json_last_error_msg());
            return [
                'success' => false,
                'error' => 'Invalid response format',
                'retry' => false
            ];
        }
        
        // Check for API errors
        if (isset($data['error']) && $data['error']) {
            return [
                'success' => false,
                'error' => $data['error'],
                'retry' => false
            ];
        }
        
        $result = [
            'success' => true,
            'data' => $data['result'] ?? $data,
            'timestamp' => time()
        ];
        
        // Cache successful response
        if ($useCache && $this->cacheEnabled) {
            $this->cache[$cacheKey] = [
                'data' => $result,
                'timestamp' => time()
            ];
        }
        
        return $result;
    }
    
    /**
     * Get admin profile
     */
    public function getAdminProfile() {
        return $this->makeRequest('/api/admin/staff/profile');
    }
    
    /**
     * Get client list
     */
    public function getClients($page = 1, $perPage = 20) {
        return $this->makeRequest('/api/admin/client/get_list', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
    
    /**
     * Get client details
     */
    public function getClient($clientId) {
        return $this->makeRequest('/api/admin/client/get', [
            'id' => $clientId
        ]);
    }
    
    /**
     * Get invoice list
     */
    public function getInvoices($page = 1, $perPage = 20, $status = null) {
        $params = [
            'page' => $page,
            'per_page' => $perPage
        ];
        
        if ($status) {
            $params['status'] = $status;
        }
        
        return $this->makeRequest('/api/admin/invoice/get_list', $params);
    }
    
    /**
     * Get invoice details
     */
    public function getInvoice($invoiceId) {
        return $this->makeRequest('/api/admin/invoice/get', [
            'id' => $invoiceId
        ]);
    }
    
    /**
     * Get order/service list
     */
    public function getOrders($page = 1, $perPage = 20) {
        return $this->makeRequest('/api/admin/order/get_list', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
    
    /**
     * Get product list
     */
    public function getProducts() {
        return $this->makeRequest('/api/admin/product/get_list', [], true);
    }
    
    /**
     * Get system statistics
     */
    public function getStats() {
        return $this->makeRequest('/api/admin/stats/get_summary', [], false); // Don't cache stats
    }
    
    /**
     * Test API connection
     */
    public function testConnection() {
        $start = microtime(true);
        $result = $this->getAdminProfile();
        $duration = round((microtime(true) - $start) * 1000);
        
        return [
            'success' => $result['success'],
            'duration' => $duration . 'ms',
            'data' => $result['success'] ? $result['data'] : null,
            'error' => $result['success'] ? null : $result['error']
        ];
    }
    
    /**
     * Get HTTP error message
     */
    private function getHttpErrorMessage($code) {
        $messages = [
            400 => 'Bad request - Invalid parameters',
            401 => 'Unauthorized - Invalid API credentials',
            403 => 'Forbidden - Access denied',
            404 => 'Not found - Endpoint does not exist',
            429 => 'Rate limit exceeded - Too many requests',
            500 => 'Internal server error - FOSSBilling server error',
            502 => 'Bad gateway - Server temporarily unavailable',
            503 => 'Service unavailable - Server maintenance',
            504 => 'Gateway timeout - Server took too long to respond'
        ];
        
        return $messages[$code] ?? 'HTTP error: ' . $code;
    }
    
    /**
     * Log error messages
     */
    private function logError($message) {
        $logFile = STORAGE_PATH . '/logs/fossbilling_api.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        $this->cache = [];
    }
    
    /**
     * Disable caching
     */
    public function disableCache() {
        $this->cacheEnabled = false;
    }
}