<?php

$username = $_SESSION['user']['username'] ?? null;

?>
<header>
    <button id="toggleSidebarBtnMin" class="toggle-btn-min">☰</button>
    <div class="logo">Pingora</div>
    <div class="search-bar">
        <input type="text" placeholder="Пошук...">
    </div>
    <div class="header-actions">
        <a href="/posts/create" class="btn">Створити</a>
        <button>Повідомлення</button>
        <?php

        if ($username === null) {
            echo '
                <a href="/login" class="user-btn">Логін</a>
            ';
        } else {
            echo '
            <div class="user-profile">
                <div class="user-avatar">' . strtoupper(mb_substr($username, 0, 1)) . '</div>
                <a href="/profile" class="user-btn">' . htmlspecialchars($username) . '</a>
            </div>
            ';
        }
        ?>

    </div>
</header>