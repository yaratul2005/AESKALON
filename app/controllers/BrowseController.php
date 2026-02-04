<?php

require_once '../core/Cache.php';

class BrowseController {
    
    public function page($type) {
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $titleMap = [
            'movie' => 'Popular Movies',
            'tv'    => 'Popular Series',
            'anime' => 'Anime Series'
        ];
        
        $pageTitle = $titleMap[$type] ?? 'Browse';
        $category = $type;
        
        ob_start();
        require_once '../app/views/browse.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }

    public function api() {
        $type = $_GET['type'] ?? 'movie';
        $page = $_GET['page'] ?? 1;
        
        $endpoint = '/discover/movie';
        if ($type == 'tv') {
            $endpoint = '/discover/tv';
        } elseif ($type == 'anime') {
            $endpoint = '/discover/tv?with_keywords=210024';
        }

        $cacheKey = "browse_{$type}_{$page}";
        $apiUrl = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY . "&page=$page";

        $data = Cache::remember($cacheKey, function() use ($apiUrl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            curl_close($ch);
            return json_decode($res, true);
        }, 43200);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode(['results' => []]);
            return;
        }

        $url = TMDB_BASE_URL . '/search/multi?api_key=' . TMDB_API_KEY . '&query=' . urlencode($query);
        
        // Cache search results for 1 hour to handle repeated typings
        $cacheKey = "search_" . md5($query);
        
        $data = Cache::remember($cacheKey, function() use ($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            curl_close($ch);
            return json_decode($res, true);
        }, 3600);

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
