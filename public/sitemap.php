<?php
require_once '../core/Database.php';

$db = Database::getInstance();
$baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
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

    <!-- Recent Movies (Optimized for SEO) -->
    <?php
    // Fetch last 50 movies
    $stmt = $db->query("SELECT id, title, release_date FROM content WHERE type = 'movie' ORDER BY created_at DESC LIMIT 50");
    while ($row = $stmt->fetch()):
    ?>
    <url>
        <loc><?= $baseUrl ?>/watch/<?= $row['id'] ?>?type=movie</loc>
        <lastmod><?= date('Y-m-d', strtotime($row['release_date'] ?? 'now')) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endwhile; ?>

    <!-- Recent Series -->
    <?php
    $stmt = $db->query("SELECT id, title, release_date FROM content WHERE type = 'tv' ORDER BY created_at DESC LIMIT 50");
    while ($row = $stmt->fetch()):
    ?>
    <url>
        <loc><?= $baseUrl ?>/watch/<?= $row['id'] ?>?type=tv</loc>
        <lastmod><?= date('Y-m-d', strtotime($row['release_date'] ?? 'now')) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endwhile; ?>

</urlset>
