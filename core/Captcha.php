<?php

require_once '../core/Database.php';

class Captcha {
    
    private static function getSetting($key) {
        $db = Database::getInstance();
        // Use query() which handles prepare/execute internally in this wrapper
        $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]); 
        return $stmt->fetchColumn();
    }

    public static function isEnabled() {
        return self::getSetting('captcha_enabled') === '1';
    }

    public static function render() {
        if (!self::isEnabled()) return '';
        
        $siteKey = self::getSetting('recaptcha_site_key');
        if (!$siteKey) return '<!-- CAPTCHA enabled but no Site Key configured -->';

        return '
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="' . htmlspecialchars($siteKey) . '" style="margin-bottom: 15px; display: flex; justify-content: center;"></div>
        ';
    }

    public static function verify($response) {
        if (!self::isEnabled()) return true;
        
        if (empty($response)) return false;

        $secret = self::getSetting('recaptcha_secret_key');
        if (!$secret) return true; // Fail open if no secret configured to prevent lockout

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secret,
            'response' => $response
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result);
        
        return $json->success;
    }
}
