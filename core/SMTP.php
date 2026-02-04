<?php

class SMTP {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $debug = [];

    public function __construct($host, $port, $user, $pass) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    private function log($msg) {
        $this->debug[] = $msg;
    }

    public function getLogs() {
        return $this->debug;
    }

    public function send($to, $subject, $body, $fromEmail, $fromName) {
        $host = $this->host;
        if ($this->port == 465 && strpos($host, 'ssl://') === false) {
            $host = 'ssl://' . $host;
        }

        $socket = fsockopen($host, $this->port, $errno, $errstr, 5); // 5s connection timeout
        if (!$socket) {
            $this->log("Error: $errno - $errstr");
            return false;
        }
        
        stream_set_timeout($socket, 5); // 5s read timeout

        $this->read($socket); // Initial greeting

        if (!$this->cmd($socket, "EHLO " . $_SERVER['SERVER_NAME'])) return false;
        
        if ($this->port == 587) {
            if (!$this->cmd($socket, "STARTTLS")) return false;
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$this->cmd($socket, "EHLO " . $_SERVER['SERVER_NAME'])) return false;
        }

        if (!$this->cmd($socket, "AUTH LOGIN")) return false;
        if (!$this->cmd($socket, base64_encode($this->user))) return false;
        if (!$this->cmd($socket, base64_encode($this->pass))) return false;

        if (!$this->cmd($socket, "MAIL FROM: <$fromEmail>")) return false;
        if (!$this->cmd($socket, "RCPT TO: <$to>")) return false;
        if (!$this->cmd($socket, "DATA")) return false;

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "From: $fromName <$fromEmail>\r\n";
        $headers .= "Reply-To: $fromEmail\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "Date: " . date("r") . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        fwrite($socket, "$headers\r\n\r\n$body\r\n.\r\n");
        $this->read($socket); // Expect 250 OK

        $this->cmd($socket, "QUIT");
        fclose($socket);
        
        return true;
    }

    private function cmd($socket, $command) {
        fwrite($socket, $command . "\r\n");
        $response = $this->read($socket);
        $code = substr($response, 0, 3);
        if ($code >= 200 && $code < 400) {
            return true;
        }
        $this->log("Command ($command) failed: $response");
        return false;
    }

    private function read($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") break;
            
            // Safety break for timeouts/meta
            $info = stream_get_meta_data($socket);
            if ($info['timed_out']) {
                $this->log("Timeout reading from SMTP");
                break;
            }
        }
        return $response;
    }
}
