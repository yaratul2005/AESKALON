<?php

class HomeController {
    private function fetchTMDB($endpoint) {
        $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, true)['results'] ?? [];
    }

    public function index() {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Categories
        $trending = $this->fetchTMDB('/trending/movie/week');
        $series = $this->fetchTMDB('/trending/tv/week');
        $anime = $this->fetchTMDB('/discover/tv?with_keywords=210024'); // Anime keyword

        $pageTitle = $settings['site_name'] ?? 'Great10';
        $pageDesc = $settings['seo_description'] ?? '';
        
        ob_start();
        require_once '../app/views/home.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }
}
