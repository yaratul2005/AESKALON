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

// Browsing Routes
$router->add('GET', '/movies', 'BrowseController', 'page', 'movie');
$router->add('GET', '/series', 'BrowseController', 'page', 'tv');
$router->add('GET', '/anime', 'BrowseController', 'page', 'anime');

// Custom Pages & Contact
$router->add('GET', '/p/([a-z0-9-]+)', 'PageController', 'show');
$router->add('POST', '/contact', 'PageController', 'contact');

// API Routes
$router->add('GET', '/api/browse', 'BrowseController', 'api');
$router->add('GET', '/api/search', 'BrowseController', 'search');

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

// Admin CMS
$router->add('GET', '/admin/pages', 'AdminController', 'pages');
$router->add('GET', '/admin/pages/new', 'AdminController', 'editPage');
$router->add('GET', '/admin/pages/edit/(\d+)', 'AdminController', 'editPage');
$router->add('GET', '/admin/pages/delete/(\d+)', 'AdminController', 'deletePage');
$router->add('POST', '/admin/pages/save', 'AdminController', 'savePage');

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($url, $scriptName) === 0) {
    $url = substr($url, strlen($scriptName));
}
if ($url === '') $url = '/';

$router->dispatch($url);
