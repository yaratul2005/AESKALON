<?php
// Config - Database & API Keys

define('DB_HOST', 'localhost');
define('DB_NAME', 'great10_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('TMDB_API_KEY', '667...48b'); // Placeholder, user will fill this
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');

define('SITE_URL', 'http://localhost:8000'); // Update this on production

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
