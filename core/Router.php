<?php
// Enhanced Router

class Router {
    private $routes = [];

    // Added optional $staticArgs to support routes like '/movies' -> page('movie')
    public function add($method, $pattern, $controller, $action, ...$staticArgs) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action,
            'staticArgs' => $staticArgs
        ];
    }

    public function dispatch($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match('#^' . $route['pattern'] . '$#', $url, $matches)) {
                array_shift($matches); // Remove full match
                
                try {
                    require_once '../app/controllers/' . $route['controller'] . '.php';
                    $controllerName = $route['controller'];
                    $controller = new $controllerName();
                    
                    $args = array_merge($route['staticArgs'], $matches);
                    
                    call_user_func_array([$controller, $route['action']], $args);
                } catch (Throwable $e) {
                    echo "<div style='background:#f87171;color:white;padding:20px;text-align:center;'>";
                    echo "<h1>System Error</h1>";
                    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
                    echo "</div>";
                }
                return;
            }
        }

        // 404
        if ($url !== '/favicon.ico') {
            echo "<h1 style='color:white;text-align:center;margin-top:50px;'>404 - Not Found</h1>";
        }
    }
}
