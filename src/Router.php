<?php
/**
 * HostView Router Class
 * Handles URL routing and request dispatching
 */

namespace HostView;

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    /**
     * Add GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Add POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Add route to routes array
     */
    private function addRoute($method, $path, $handler) {
        $path = $this->basePath . '/' . trim($path, '/');
        $path = $path ?: '/';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    /**
     * Dispatch current request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if present
        if ($this->basePath && strpos($requestPath, $this->basePath) === 0) {
            $requestPath = substr($requestPath, strlen($this->basePath));
        }
        
        $requestPath = $requestPath ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
                return $this->handleRoute($route['handler']);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        $this->render404();
    }
    
    /**
     * Check if path matches route
     */
    private function matchPath($routePath, $requestPath) {
        return $routePath === $requestPath;
    }
    
    /**
     * Handle route execution
     */
    private function handleRoute($handler) {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controllerName, $methodName) = explode('@', $handler, 2);
            
            $controllerClass = "\\HostView\\Controllers\\{$controllerName}";
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $methodName)) {
                    return $controller->$methodName();
                } else {
                    throw new \Exception("Method {$methodName} not found in {$controllerClass}");
                }
            } else {
                throw new \Exception("Controller {$controllerClass} not found");
            }
        } elseif (is_callable($handler)) {
            return call_user_func($handler);
        } else {
            throw new \Exception("Invalid route handler");
        }
    }
    
    /**
     * Render 404 page
     */
    private function render404() {
        echo '<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
        p { color: #666; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The page you are looking for could not be found.</p>
    <p><a href="/">Return to Home</a></p>
</body>
</html>';
    }
}