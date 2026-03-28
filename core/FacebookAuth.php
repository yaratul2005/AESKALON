<?php

class FacebookAuth {
    private $appId;
    private $appSecret;
    private $redirectUri;

    public function __construct($appId, $appSecret, $redirectUri) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->redirectUri = $redirectUri;
    }

    public function getAuthUrl() {
        return "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'state' => bin2hex(random_bytes(16)),
            'scope' => 'email,public_profile'
        ]);
    }

    public function getToken($code) {
        $url = "https://graph.facebook.com/v18.0/oauth/access_token";
        $params = [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code
        ];

        $ch = curl_init($url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getUserInfo($accessToken) {
        $url = "https://graph.facebook.com/v18.0/me?fields=id,name,email,picture.type(large)&access_token=" . $accessToken;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['id'])) {
            return [
                'id' => $data['id'],
                'name' => $data['name'] ?? '',
                'email' => $data['email'] ?? '',
                'picture' => $data['picture']['data']['url'] ?? ''
            ];
        }
        return false;
    }
}
