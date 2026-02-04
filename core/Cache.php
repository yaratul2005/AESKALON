<?php

class Cache {
    private $db;
    private $duration; // in seconds

    public function __construct($duration = 21600) { // Default 6 hours
        $this->db = Database::getInstance();
        $this->duration = $duration;
    }

    public function get($endpoint) {
        $hash = md5($endpoint);
        
        $stmt = $this->db->query("SELECT * FROM api_cache WHERE endpoint_hash = ? AND expires_at > NOW()", [$hash]);
        $row = $stmt->fetch();
        
        if ($row) {
            return json_decode($row['data'], true);
        }
        return null;
    }

    public function set($endpoint, $data) {
        $hash = md5($endpoint);
        $json = json_encode($data);
        $expires = date('Y-m-d H:i:s', time() + $this->duration);
        
        // Upsert
        $sql = "INSERT INTO api_cache (endpoint_hash, data, expires_at) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE data = ?, expires_at = ?";
        
        $this->db->query($sql, [$hash, $json, $expires, $json, $expires]);
    }

    // Static helper for quick fetch
    public static function remember($endpoint, $callback, $duration = 21600) {
        $cache = new self($duration);
        $data = $cache->get($endpoint);
        
        if ($data !== null) {
            return $data;
        }

        $data = $callback();
        
        if ($data) {
            $cache->set($endpoint, $data);
        }
        
        return $data;
    }
}
