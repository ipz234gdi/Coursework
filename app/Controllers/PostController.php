<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/ChannelController.php';

class PostController
{
    public function index()
    {
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

    public function showCreatePost()
    {
        // 1) Отримати список спільнот для сайдбару
        $communities = (new ChannelController())->listUserCommunities();
        $activeCommunityId = null;
        $filter = ''; // тут фільтр неактуальний

        // 2) Підготувати контент форми у буфері
        $content = view('posts/create'); 

        // 3) Відрендерити layout
        echo view('layouts/layout', [
            'title'             => 'Створити пост',
            'communities'       => $communities,
            'activeCommunityId' => $activeCommunityId,
            'filter'            => $filter,
            'content'           => $content,
        ]);
    }

    public function createPost()
    {
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

    /**
     * Основний метод: повертає масив постів за фільтром.
     * @param string $filter  'home'|'latest'|'popular'|'all'
     * @param int|null $userId
     * @return array
     */

     public function listPosts(string $filter = 'all', ?int $userId = null): array
     {
         $pdo = db();
 
         switch ($filter) {
             case 'home':
                 if ($userId === null) {
                     // якщо не залогінений — показуємо порожній список
                     echo '<script> log.console("User not found"); </script>';
                     return [];
                 }
                 $sql = "
                     SELECT p.*, COALESCE(u.username,'[deleted]') as username
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     WHERE p.channel_id IN (
                         SELECT c.id
                         FROM channels c
                         LEFT JOIN channel_members m ON c.id = m.channel_id
                         WHERE c.created_by = :uid OR m.user_id = :uid
                     )
                     ORDER BY p.created_at DESC
                 ";
                 $stmt = $pdo->prepare($sql);
                 $stmt->execute(['uid' => $userId]);
                 break;
 
             case 'latest':
                 $sql = "
                     SELECT p.*, COALESCE(u.username,'[deleted]') as username
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     ORDER BY p.created_at DESC
                 ";
                 $stmt = $pdo->query($sql);
                 break;
 
             case 'popular':
                 // тут пізніше зробимо сортування за лайками, поки — за переглядами
                 $sql = "
                     SELECT p.*, COALESCE(u.username,'[deleted]') as username
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     ORDER BY p.views DESC
                 ";
                 $stmt = $pdo->query($sql);
                 break;
 
             default:
                 // 'all' або невідомий фільтр
                 $sql = "
                     SELECT p.*, COALESCE(u.username,'[deleted]') as username
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     ORDER BY p.created_at DESC
                 ";
                 $stmt = $pdo->query($sql);
                 break;
         }
 
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
}

?>