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
                
                require_once '../app/controllers/' . $route['controller'] . '.php';
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                
                // Merge static args (e.g., 'movie') with Regex params (e.g., ID)
                // If staticArgs are present, they come FIRST in this design or LAST?
                // Let's pass staticArgs first, then regex matches.
                // Re-thinking: Usually logic is specific. For '/movies', no regex args.
                // For '/watch/123', regex args.
                
                $args = array_merge($route['staticArgs'], $matches);
                
                call_user_func_array([$controller, $route['action']], $args);
                return;
            }
        }

        // 404
        if ($url !== '/favicon.ico') {
            echo "<h1 style='color:white;text-align:center;margin-top:50px;'>404 - Not Found</h1>";
        }
    }
}
