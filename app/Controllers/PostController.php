<?php

require_once __DIR__ . '/../../config/database.php';

class PostController {
    public function index() {
        try {
            $pdo = db();
            $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($posts, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // 
    public function showCreatePost() {
        $title = "Створити пост";
        ob_start();
        require __DIR__ . '/../Views/posts/create.php';
        $CreatePost = ob_get_clean();
        require __DIR__ . '/../Views/main.php';
    }

    public function createPost() {
        // session_start();/
        // require_once __DIR__ . '/../../config/database.php';

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $media_url = trim($_POST['media_url'] ?? '') ?: null;

        if (!$title || !$content || !isset($_SESSION['user'])) {
            echo "Дані некоректні або ви не авторизовані.";
            return;
        }

        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO posts (channel_id, user_id, title, content, media_url, views, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
        $stmt->execute([null, $_SESSION['user']['id'], $title, $content, $media_url]);

        header("Location: /home");
        exit;
    }
}

?>