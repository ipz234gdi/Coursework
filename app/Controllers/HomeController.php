<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../Controllers/PostController.php';
require_once __DIR__ . '/../Controllers/ChannelController.php';

class HomeController
{
    public function index()
    {

        $filter = $_GET['filter'] ?? 'home';
        $userId = $_SESSION['user']['id'] ?? null;

        // 1) Дістаємо пости
        $posts = (new PostController())->listPosts($filter, $userId);

        // 2) Дістаємо спільноти для aside
        $communities = (new ChannelController())->listUserCommunities();

        // 3) Формуємо контент «main»
        $content = view('posts/post_list', [
            'posts' => $posts,
            'filter' => $filter,
        ]);

        // 4) Рендеримо layout, передаючи все, що потрібно
        echo view('layouts/layout', [
            'title'               => 'Головна — Pingora',
            'communities'         => $communities,
            'activeCommunityId'   => $_GET['active'] ?? null,
            'filter'              => $filter,
            'content'             => $content,
        ]);
    }
}
