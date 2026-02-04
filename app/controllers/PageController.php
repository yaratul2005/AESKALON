<?php

require_once '../core/SMTP.php';

class PageController {
    
    public function show($slug) {
        $db = Database::getInstance();
        $page = $db->query("SELECT * FROM pages WHERE slug = ?", [$slug])->fetch();
        
        if (!$page) {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found";
            return;
        }

        $pageTitle = $page['title'];
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        ob_start();
        require_once '../app/views/page.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }

    public function contact() {
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (!$email || !$message) {
            echo "Email and Message required.";
            return;
        }

        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        $smtp = new SMTP($s['smtp_host'], $s['smtp_port'], $s['smtp_user'], $s['smtp_pass']);
        
        $body = "<h3>New Contact Message</h3>";
        $body .= "<p><strong>From:</strong> $email</p>";
        $body .= "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

        // Send to Admin (using configured 'from' email or smtp user as destination)
        $adminEmail = $s['smtp_from_email'] ?? $s['smtp_user'];
        $success = $smtp->send(
            $adminEmail, 
            "Contact Form: $email", 
            $body, 
            $s['smtp_from_email'], 
            "Great10 Contact"
        );

        if ($success) {
            // Simple feedback
            echo "<script>alert('Message sent successfully!'); window.location.href='/';</script>";
        } else {
            echo "<script>alert('Failed to send message.'); window.history.back();</script>";
        }
    }
}
