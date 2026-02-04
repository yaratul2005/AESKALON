<?php

class CommentController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function getComments() {
        $contentId = $_GET['id'] ?? 0;
        $type = $_GET['type'] ?? 'movie';
        
        $db = Database::getInstance();
        $comments = $db->query(
            "SELECT c.*, u.username, u.avatar 
             FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.content_id = ? AND c.type = ? 
             ORDER BY c.created_at DESC", 
            [$contentId, $type]
        )->fetchAll();

        // Organize into threads (Parent -> Replies)
        $threads = [];
        $replies = [];
        
        foreach ($comments as $c) {
            if ($c['parent_id']) {
                $replies[$c['parent_id']][] = $c;
            } else {
                $threads[] = $c;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['threads' => $threads, 'replies' => $replies]);
    }

    public function postComment() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user_id'];
        $contentId = $input['id'] ?? 0;
        $type = $input['type'] ?? 'movie';
        $body = trim($input['body'] ?? '');
        $parentId = $input['parentId'] ?? null;

        if (empty($body)) exit(json_encode(['error' => 'Empty comment']));

        $db = Database::getInstance();
        $db->query(
            "INSERT INTO comments (user_id, content_id, type, parent_id, body) VALUES (?, ?, ?, ?, ?)", 
            [$userId, $contentId, $type, $parentId, $body]
        );

        echo json_encode(['status' => 'success', 'user' => $_SESSION['user_username']]);
    }
}
