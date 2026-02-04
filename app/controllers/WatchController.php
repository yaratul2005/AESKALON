<?php

class WatchController {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function fetchTMDB($endpoint) {
        $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return json_decode(curl_exec($ch), true);
    }

    public function index($id) {
        $type = $_GET['type'] ?? 'movie'; // 'movie' or 'tv'
        $season = $_GET['s'] ?? 1;
        $episode = $_GET['e'] ?? 1;

        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        $movie = $this->fetchTMDB("/$type/$id");
        
        if (!$movie || isset($movie['success']) && !$movie['success']) {
            echo "Content not found."; return;
        }

        // Logic for Series
        $seasons = [];
        $episodes = [];
        if ($type == 'tv') {
            $seasons = $movie['seasons'] ?? [];
            // Fetch episodes for current season
            $seasonData = $this->fetchTMDB("/tv/$id/season/$season");
            $episodes = $seasonData['episodes'] ?? [];
        }

        $title = $movie['title'] ?? $movie['name'];
        $pageTitle = "Watch " . $title;
        $pageDesc = $movie['overview'] ?? '';

        ob_start();
        require_once '../app/views/watch.php';
        $content = ob_get_clean();

        // SEO Data
        $pageTitle = "Watch " . ($content['title'] ?? $content['name']) . " - Great10";
        $pageDesc = mb_substr($content['overview'] ?? 'Watch this amazing title on Great10.', 0, 160) . '...';
        $pageImage = "https://image.tmdb.org/t/p/w780" . ($content['backdrop_path'] ?? $content['poster_path']);

        require_once '../app/views/layout.php';
    }
}
