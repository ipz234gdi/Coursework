<?php

require_once __DIR__ . '/../../config/database.php';

class ChannelController
{
    public function listUserCommunities(): array {
        $communities = [];
        $activeCommunityId = $_GET['active'] ?? null;
    
        if (isset($_SESSION['user']['id'])) {
            $userId = $_SESSION['user']['id'];
            $pdo = db();
            $communities = $this->getUserCommunities($pdo, $userId);
        }
        echo "<script>console.log('Get all channals to user');</script>";
        // require __DIR__ . '/../Views/layouts/aside.php';
        return $communities;
    }

    function getUserCommunities(PDO $pdo, int $userId): array {
        $stmt = $pdo->prepare("
            SELECT DISTINCT c.*
            FROM channels c
            LEFT JOIN channel_members m ON c.id = m.channel_id
            WHERE c.created_by = :user_id OR m.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        echo "<script>console.log('Get all channals');</script>";
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createChannels()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Method not allowed";
            return;
        }

        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $hashtags = $_POST['hashtags'] ?? '';
        $privacy = $_POST['privacy'] ?? 'open';
        $is_18 = isset($_POST['is_18']) ? 1 : 0;
        $created_by = $_SESSION['user']['id'] ?? null;

        // todo: збереження іконки та банера
        $icon = $_FILES['icon']['name'] ?? null;
        $banner = $_FILES['banner']['name'] ?? null;

        // завантаження файлів
        if ($icon) {
            move_uploaded_file($_FILES['icon']['tmp_name'], "uploads/icons/$icon");
        }
        if ($banner) {
            move_uploaded_file($_FILES['banner']['tmp_name'], "uploads/banners/$banner");
        }

        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO channels (name, description, is_private, created_by, created_at, `+18`)
            VALUES (?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([
            $name,
            $description,
            $privacy === 'private' ? 1 : 0,
            $created_by,
            $is_18
        ]);

        header("Location: /channels");
        exit;
    }

    public function show($id)
    {
        // session_start();
        $pdo = db();

        // 1) Отримати дані спільноти
        $stmt = $pdo->prepare("SELECT * FROM channels WHERE id = ?");
        $stmt->execute([$id]);
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$channel) {
            http_response_code(404);
            echo "Спільнота не знайдена";
            exit;
        }

        // 2) Лічильники
        $memberCount = $pdo
            ->prepare("SELECT COUNT(*) FROM channel_members WHERE channel_id = ?")
            ->execute([$id]) 
            ? $pdo->prepare("SELECT COUNT(*) FROM channel_members WHERE channel_id = ?")->fetchColumn()
            : 0;

        // 3) Останні пости в спільноті
        $stmt = $pdo->prepare("
            SELECT p.*, COALESCE(u.username,'[deleted]') AS username
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.channel_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4) Підсвітки спільноти (“Community highlights”) — наприклад, 3 топ‑пости
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.media_url, p.views 
            FROM posts p
            WHERE p.channel_id = ?
            ORDER BY p.views DESC
            LIMIT 3
        ");
        $stmt->execute([$id]);
        $highlights = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5) Чи підписаний поточний юзер?
        $userId = $_SESSION['user']['id'] ?? null;
        $isJoined = false;
        if ($userId) {
            $check = $pdo->prepare("
                SELECT 1 FROM channel_members 
                WHERE channel_id = ? AND user_id = ?
            ");
            $check->execute([$id, $userId]);
            $isJoined = (bool)$check->fetchColumn();
        }
        // Доставка комюнити
        $communities = $this->listUserCommunities();
        $activeCommunityId = (int)$id;
        $filter = 'channels';

        ob_start();
        include __DIR__ . '/../Views/channels/show.php';
        $content = ob_get_clean();

        // 6) Підготовка й рендер
        $title = 'p/' . htmlspecialchars($channel['name']) . ' — Pingora';
        require __DIR__ . '/../Views/layouts/layout.php';
    }
}


?>