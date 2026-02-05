<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getInstance();
$baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
// Helper to fetch/cache TMDB data for Sitemap
require_once '../core/Cache.php';

function fetchSitemapData($endpoint, $key) {
    if (!defined('TMDB_API_KEY')) return [];
    
    $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY;
    return Cache::remember('sitemap_' . $key, function() use ($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, true)['results'] ?? [];
    }, 43200); // 12 Hours Cache
}

$movies = fetchSitemapData('/trending/movie/week', 'movies');
$series = fetchSitemapData('/trending/tv/week', 'series');
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?= $baseUrl ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/movies</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/series</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Trending Movies -->
    <?php foreach ($movies as $item): ?>
    <url>
        <loc><?= $baseUrl ?>/watch/<?= $item['id'] ?>?type=movie</loc>
        <lastmod><?= isset($item['release_date']) ? date('Y-m-d', strtotime($item['release_date'])) : date('Y-m-d') ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- Trending Series -->
    <?php foreach ($series as $item): ?>
    <url>
        <loc><?= $baseUrl ?>/watch/<?= $item['id'] ?>?type=tv</loc>
        <lastmod><?= isset($item['first_air_date']) ? date('Y-m-d', strtotime($item['first_air_date'])) : date('Y-m-d') ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

</urlset>
