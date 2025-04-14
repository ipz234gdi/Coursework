<?php

require_once __DIR__ . '/../../config/database.php';

class ChannelController
{
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

    public function listUserCommunities() {
        $communities = [];
        $activeCommunityId = $_GET['active'] ?? null;
    
        if (isset($_SESSION['user']['id'])) {
            $userId = $_SESSION['user']['id'];
            $pdo = db();
            $communities = $this->getUserCommunities($pdo, $userId);
        }
        echo "<script>console.log('Get all channals to user');</script>";
        // Вивід шаблону aside.php (він отримає $communities)
        require __DIR__ . '/../Modules/aside.php';
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
}


?>