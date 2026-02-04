<?php

require_once '../config/config.php';
require_once '../core/Router.php';
require_once '../core/Database.php';

function asset($path) {
    return SITE_URL . '/' . ltrim($path, '/');
}

$router = new Router();

// Routes
$router->add('GET', '/', 'HomeController', 'index');
$router->add('GET', '/watch/(\d+)', 'WatchController', 'index');

// Browsing Routes (Page View)
$router->add('GET', '/movies', 'BrowseController', 'page', 'movie');
$router->add('GET', '/series', 'BrowseController', 'page', 'tv');
$router->add('GET', '/anime', 'BrowseController', 'page', 'anime');

// API Route (JSON)
$router->add('GET', '/api/browse', 'BrowseController', 'api');

// Admin Routes
$router->add('GET', '/admin', 'AdminController', 'login');
$router->add('GET', '/admin/logout', 'AdminController', 'logout');
$router->add('POST', '/admin/auth', 'AdminController', 'auth');
$router->add('GET', '/admin/dashboard', 'AdminController', 'dashboard');
$router->add('GET', '/admin/settings', 'AdminController', 'settings');
$router->add('POST', '/admin/update', 'AdminController', 'updateSettings');
$router->add('GET', '/admin/users', 'AdminController', 'users');
$router->add('POST', '/admin/ban-ip', 'AdminController', 'banIp');
$router->add('POST', '/admin/unban-ip', 'AdminController', 'unbanIp');
$router->add('POST', '/admin/run-updates', 'AdminController', 'runUpdates');
$router->add('GET', '/admin/test-smtp', 'AdminController', 'testSmtp');

// Dispatch Setup
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($url, $scriptName) === 0) {
    $url = substr($url, strlen($scriptName));
}
if ($url === '') $url = '/';

// Custom Dispatch Logic for Fixed Arguments (like 'movie', 'tv')
// Since our simple Router didn't support passing extra args defined in add(),
// We need to modify Router class OR handle it here. 
// Easier to modify Router class quickly. I will update Router.php next.

$router->dispatch($url);
