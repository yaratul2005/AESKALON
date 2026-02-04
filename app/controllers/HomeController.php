<?php

require_once '../core/Cache.php';

class HomeController {
    
    // Helper to fetch (now with Cache!)
    private function fetchTMDB($endpoint, $cacheKey) {
        $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY;
        
        return Cache::remember($cacheKey, function() use ($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
            $data = curl_exec($ch);
            curl_close($ch);
            return json_decode($data, true)['results'] ?? [];
        }, 10800); // 3 hours cache for home
    }

    public function index() {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Categories with Caching
        $trending = $this->fetchTMDB('/trending/movie/week', 'home_trending');
        $series = $this->fetchTMDB('/trending/tv/week', 'home_series');
        $anime = $this->fetchTMDB('/discover/tv?with_keywords=210024', 'home_anime');

        $pageTitle = $settings['site_name'] ?? 'Great10';
        $pageDesc = $settings['seo_description'] ?? '';
        
        ob_start();
        require_once '../app/views/home.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }
}
