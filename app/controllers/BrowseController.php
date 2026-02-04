<?php

require_once '../core/Cache.php';

class BrowseController {
    
    public function page($type) {
        $db = Database::getInstance();
        // Cache settings? No, settings change rarely but we want instant Admin updates. 
        // We could cache them too but let's stick to heavy API calls first.
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

        // Full URL for fetching, but for Caching Key we can just use the unique params
        $cacheKey = "browse_{$type}_{$page}";
        $apiUrl = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY . "&page=$page";

        // Use Cache Wrapper
        $data = Cache::remember($cacheKey, function() use ($apiUrl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            curl_close($ch);
            return json_decode($res, true);
        }, 43200); // Cache for 12 hours

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
