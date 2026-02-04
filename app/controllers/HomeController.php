<?php

class HomeController {
    public function index() {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Trending Movies from TMDB
        // Using curl to handle potential SSL issues better than file_get_contents
        $url = TMDB_BASE_URL . '/trending/movie/week?api_key=' . TMDB_API_KEY;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $movies = [];
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $movies = $data['results'] ?? [];
        }

        // Pass data to view
        $pageTitle = $settings['site_name'] ?? 'Great10';
        $pageDesc = $settings['seo_description'] ?? '';
        
        // Output buffering to capture view content
        ob_start();
        require_once '../app/views/home.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }
}
