<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../../config/database.php';

class ChannelController
{
    public function listUserCommunities(): array
    {
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

    function getUserCommunities(PDO $pdo, int $userId): array
    {
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

    //==================CREATE=====================
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

        // після успішного виконання INSERT INTO channels (…)
        $channelId = $pdo->lastInsertId();

        // Створюємо ролі для цього каналу
        $roleInsert = $pdo->prepare("
            INSERT INTO channel_roles (channel_id, name) 
            VALUES 
                (?, 'owner'),
                (?, 'moderator'),
                (?, 'member')
            ");
        $roleInsert->execute([$channelId, $channelId, $channelId]);

        // Додаємо в члени самого автора як owner
        $userId = $_SESSION['user']['id'] ?? null;
        $ownerRoleId = $pdo->lastInsertId(); // або виберіть з таблиці
        $memberInsert = $pdo->prepare("
            INSERT INTO channel_members (channel_id, user_id, role_id, joined_at)
            VALUES (?, ?, ?, NOW())
            ");
        $memberInsert->execute([$channelId, $userId, $ownerRoleId]);

        header("Location: /channels/{$name}");
        exit;
    }

    //==================SHOW=====================
    public function show(string $name)
    {
        // session_start();
        $pdo = db();

        // 1) Отримати дані спільноти
        $stmt = $pdo->prepare("SELECT * FROM channels WHERE name = ?");
        $stmt->execute([$name]);
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$channel) {
            http_response_code(404);
            echo "Спільнота не знайдена";
            exit;
        }

        $channelId = (int) $channel['id'];

        // 2) Лічильники
        $memberCount = $pdo
            ->prepare("SELECT COUNT(*) FROM channel_members WHERE channel_id = ?")
            ->execute([$channelId])
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
        $stmt->execute([$channelId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4) Підсвітки спільноти (“Community highlights”) — наприклад, 3 топ‑пости
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.media_url, p.views 
            FROM posts p
            WHERE p.channel_id = ?
            ORDER BY p.views DESC
            LIMIT 3
        ");
        $stmt->execute([$channelId]);
        $highlights = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5) Чи підписаний поточний юзер?
        $userId = $_SESSION['user']['id'] ?? null;
        $isJoined = false;
        if ($userId) {
            $check = $pdo->prepare("
                SELECT 1 FROM channel_members 
                WHERE channel_id = ? AND user_id = ?
            ");
            $check->execute([$channelId, $userId]);
            $isJoined = (bool) $check->fetchColumn();
        }
        // Доставка комюнити
        $communities = $this->listUserCommunities();

        $filter = 'channels';

        // 6) Підготовка й рендер
        $title = 'p/' . htmlspecialchars($channel['name']) . ' — Pingora';

        $content = view('channels/show', [
            'channel' => $channel,
            'isJoined' => $isJoined,
            'posts' => $posts,
            'highlights' => $highlights,
            'memberCount' => $memberCount,
            'userId' => $userId,
        ]);

        echo view('layouts/layout', [
            'title' => $title,
            'communities' => $communities,
            'activeCommunityId' => $_GET['active'] ?? null,
            'filter' => $filter,
            'content' => $content,
        ]);
    }

    //==================JOIN=====================
    public function join(string $name)
    {
        $pdo = db();

        // 1) Має бути залогінений юзер
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        // 2) Знайти канал по назві та отримати його id і created_by
        $stmt = $pdo->prepare("
            SELECT id, created_by
            FROM channels
            WHERE name = ?
            LIMIT 1
        ");
        $stmt->execute([$name]);
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$channel) {
            http_response_code(404);
            echo "Спільнота не знайдена";
            exit;
        }
        $channelId = (int) $channel['id'];

        // 3) Не даємо приєднатися автору каналу
        if ((int) $channel['created_by'] === $userId) {
            // або просто редирект без кнопки
            header("Location: /channels/{$name}");
            exit;
        }

        // 4) Перевірити, чи вже є в членах
        $check = $pdo->prepare("
            SELECT 1
              FROM channel_members
             WHERE channel_id = ? AND user_id = ?
        ");
        $check->execute([$channelId, $userId]);
        if (!$check->fetchColumn()) {
            // 5) Додати в члени зі стандартною роллю member
            // Припустимо, що у вас у channel_roles є запис з name='member'
            $roleStmt = $pdo->prepare("
                SELECT role_id
                  FROM channel_roles
                 WHERE channel_id = ? AND name = 'member'
                LIMIT 1
            ");
            $roleStmt->execute([$channelId]);
            $memberRole = $roleStmt->fetchColumn() ?: null;

            $ins = $pdo->prepare("
                INSERT INTO channel_members (channel_id, user_id, role_id, joined_at)
                VALUES (?, ?, ?, NOW())
            ");
            $ins->execute([$channelId, $userId, $memberRole]);
        }

        // 6) Назад на сторінку каналу
        header("Location: /channels/{$name}");
        exit;
    }

    //==================LEAVE=====================
    public function leave(string $name)
    {
        $pdo = db();

        // Перевірка авторизації
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        // Знаходимо канал по назві, щоб узяти його id
        $stmt = $pdo->prepare("SELECT id FROM channels WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$channel) {
            http_response_code(404);
            echo "Спільнота не знайдена";
            exit;
        }
        $channelId = (int) $channel['id'];

        // Видаляємо запис про членство
        $del = $pdo->prepare("
        DELETE FROM channel_members
         WHERE channel_id = ? AND user_id = ?
    ");
        $del->execute([$channelId, $userId]);

        // Редірект назад на сторінку каналу
        header("Location: /channels/{$name}");
        exit;
    }

}


?>