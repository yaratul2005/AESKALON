<?php

require_once '../config/config.php';
require_once '../core/Router.php';
require_once '../core/Database.php';

// Helper function for Assets
function asset($path) {
    return SITE_URL . '/' . ltrim($path, '/');
}

// Router Setup
$router = new Router();

// Routes
$router->add('GET', '/', 'HomeController', 'index');
$router->add('GET', '/watch/(\d+)', 'WatchController', 'index');

// Admin Routes
$router->add('GET', '/admin', 'AdminController', 'login');
$router->add('POST', '/admin/auth', 'AdminController', 'auth');
$router->add('GET', '/admin/dashboard', 'AdminController', 'dashboard');
$router->add('POST', '/admin/update', 'AdminController', 'updateSettings');
$router->add('POST', '/admin/run-updates', 'AdminController', 'runUpdates');

// Dispatch
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Handle subdirectory deployment if consistent with config
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($url, $scriptName) === 0) {
    $url = substr($url, strlen($scriptName));
}
if ($url === '') $url = '/';

$router->dispatch($url);
