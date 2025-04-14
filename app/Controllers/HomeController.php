<?php
class HomeController
{
    public function index()
    {

        require_once __DIR__ . '/../../config/database.php';
        $pdo = db();

        $filter = $_GET['filter'] ?? 'home';
        $userId = $_SESSION['user']['id'] ?? null;

        if ($filter === 'home' && $userId !== null) {
            // Показати пости лише з каналів, де користувач учасник або автор
            $stmt = $pdo->prepare("
            SELECT posts.*, users.username 
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.channel_id IN (
                SELECT c.id
                FROM channels c
                LEFT JOIN channel_members m ON c.id = m.channel_id
                WHERE c.created_by = :uid OR m.user_id = :uid
            )
            ORDER BY posts.created_at DESC
        ");
            $stmt->execute(['uid' => $userId]);

        } elseif ($filter === 'latest') {
            $stmt = $pdo->query("
            SELECT posts.*, COALESCE(users.username, '[deleted]') AS username
            FROM posts
            LEFT JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
        ");
        } elseif ($filter === 'popular') {
            // тимчасова заглушка (в майбутньому сортувати за лайками)
            $stmt = $pdo->query("
            SELECT posts.*, users.username 
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.views DESC
        ");
        } else {
            // fallback (усі пости)
            $stmt = $pdo->query("
            SELECT posts.*, users.username 
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
        ");
        }

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = 'Головна - Pingora';
        $communities = [];
        $activeCommunityId = $_GET['active'] ?? null;

        require_once __DIR__ . '/../Controllers/ChannelController.php';
        
        ob_start();
        
        $controller = new ChannelController();
        $controller->listUserCommunities(); // виклик методу

        // require __DIR__ . '/../Modules/aside.php';
        require __DIR__ . '/../Modules/main.php';

        
        $ListPost = ob_get_clean();
        echo "<script>console.log(" . json_encode($controller) . ");</script>";
        echo "<script>console.log(" . json_encode($posts) . ");</script>";

        // echo $ListPost;

        // require __DIR__ . '/../Views/home.php';

        require __DIR__ . '/../Views/main.php';
    }
}
