<?php

class BrowseController {
    
    public function index($type = 'movie') {
        $page = $_GET['page'] ?? 1;
        $endpoint = ($type == 'tv') ? '/discover/tv' : '/discover/movie';
        
        // Anime Filter
        if ($type == 'anime') {
            $endpoint = '/discover/tv?with_keywords=210024';
        }

        $url = TMDB_BASE_URL . $endpoint . (strpos($endpoint, '?') ? '&' : '?') . 'api_key=' . TMDB_API_KEY . "&page=$page";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        echo curl_exec($ch);
        curl_close($ch);
    }
}
