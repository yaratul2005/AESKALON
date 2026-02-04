<?php
// Simple Router

class Router {
    private $routes = [];

    public function add($method, $pattern, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match('#^' . $route['pattern'] . '$#', $url, $matches)) {
                array_shift($matches); // Remove full match
                
                require_once '../app/controllers/' . $route['controller'] . '.php';
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                
                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }

        // 404 Not Found
        echo "404 Not Found";
    }
}
