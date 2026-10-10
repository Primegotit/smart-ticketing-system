<?php

namespace app\core;

class Router {

    protected $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function get($path, $controller) 
    {
        $this->routes['GET'][$path] = $controller;
    }

    public function post($path, $controller) 
    {
        $this->routes['POST'][$path] = $controller;
    }

    public function dispatch($url)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = trim($url, '/');

        // 1. Check static routes first
        if (isset($this->routes[$method][$url])) {
            $this->callController($this->routes[$method][$url], []);
            return;
        }

        // 2. Check dynamic routes
        foreach ($this->routes[$method] as $route => $controllerAction) {
            $pattern = $this->convertToRegex($route);
            if (preg_match($pattern, $url, $matches)) {
                // Extract only the named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callController($controllerAction, $params);
                return;
            }
        }

        // 3. No route found
        http_response_code(404);
        echo "<div class='container mt-5'><div class='alert alert-danger'>";
        echo "<h1>404 Not Found</h1><p>The page you requested was not found.</p>";
        echo "</div></div>";
    }

    protected function convertToRegex($route) {
        // Escape forward slashes
        $pattern = preg_replace('/\//', '\\/', $route);
        
        // Convert route variables to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^\/]+)', $pattern);
        
        // Add start and end delimiters
        return '/^' . $pattern . '$/i';
    }

    protected function callController($controller, $params = []) {
        [$class, $method] = explode('@', $controller);
        $class = "app\\controllers\\" . $class;
        
        if (class_exists($class)) {
            $controllerInstance = new $class();
            
            if (method_exists($controllerInstance, $method)) {
                // Use reflection to call method with named parameters
                $reflection = new \ReflectionMethod($controllerInstance, $method);
                $orderedParams = [];
                
                foreach ($reflection->getParameters() as $param) {
                    $paramName = $param->getName();
                    $orderedParams[] = $params[$paramName] ?? null;
                }
                
                call_user_func_array([$controllerInstance, $method], $orderedParams);
            } else {
                die("Method $method not found in $class");
            }
        } else {
            die("Controller $class not found");
        }
    }
}