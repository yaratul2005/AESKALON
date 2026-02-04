<?php

class WatchController {
    public function index($tmdbId) {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        // Fetch Movie Details
        $url = TMDB_BASE_URL . '/movie/' . $tmdbId . '?api_key=' . TMDB_API_KEY;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $movie = json_decode($response, true);
        if (!$movie || isset($movie['success']) && !$movie['success']) {
            echo "Movie not found.";
            return;
        }

        $pageTitle = "Watch " . $movie['title'];
        $pageDesc = $movie['overview'] ?? '';

        ob_start();
        require_once '../app/views/watch.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }
}
