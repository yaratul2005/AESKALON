<?php
// scripts/cron.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

echo "Running Great10 TMDB Background Indexer...\n";

function fetchTMDB($endpoint) {
    $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    // Allow local environments to bypass cURL SSL issues easily
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// Ensure cache directory exists
$cacheDir = __DIR__ . '/../storage/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// 1. Fetch Upcoming Movies
$upcoming = fetchTMDB('/movie/upcoming?region=US');
if (isset($upcoming['results'])) {
    file_put_contents($cacheDir . '/upcoming.json', json_encode($upcoming['results']));
    echo "Cached " . count($upcoming['results']) . " Upcoming Movies.\n";
}

// 2. Fetch Trending / Popular
$trending = fetchTMDB('/trending/all/week');
if (isset($trending['results'])) {
    file_put_contents($cacheDir . '/trending.json', json_encode($trending['results']));
    echo "Cached " . count($trending['results']) . " Trending Titles.\n";
}

echo "Cron cycle complete. Time: " . date('Y-m-d H:i:s') . "\n";
