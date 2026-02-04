<?php

class BrowseController {
    
    // Render the HTML Page
    public function page($type) {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $titleMap = [
            'movie' => 'Popular Movies',
            'tv'    => 'Popular Series',
            'anime' => 'Anime Series'
        ];
        
        $pageTitle = $titleMap[$type] ?? 'Browse';
        $category = $type; // passed to JS
        
        ob_start();
        require_once '../app/views/browse.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }

    // JSON API for Infinite Scroll
    public function api() {
        $type = $_GET['type'] ?? 'movie';
        $page = $_GET['page'] ?? 1;
        
        $endpoint = '/discover/movie';
        if ($type == 'tv') {
            $endpoint = '/discover/tv';
        } elseif ($type == 'anime') {
            $endpoint = '/discover/tv?with_keywords=210024';
        }

        $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY . "&page=$page";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        
        header('Content-Type: application/json');
        echo $response;
    }
}
