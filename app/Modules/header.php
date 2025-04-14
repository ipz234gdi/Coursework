<?php

$username = $_SESSION['user']['username'] ?? null;

?>
<header>
    <button id="toggleSidebarBtnMin" class="toggle-btn-min">☰</button>
    <div class="logo"><a href="/home">Pingora</a></div>
    <div class="search-bar">
        <input type="text" placeholder="Пошук...">
    </div>
    <div class="header-actions">
        <a href="/posts/create" class="btn">
            <p>Створити</p>
        </a>
        <?php

        if ($username === null) {
            echo '
                <a href="/login" class="user-btn btn">Логін</a>
            ';
        } else {
            echo '
            <button id="userProfileBtn" class="user-profile btn" onclick="showModalProfile()">
                <div class="user-avatar">' . strtoupper(mb_substr($username, 0, 1)) . '</div>
                <p class="user-btn">' . htmlspecialchars($username) . '</p>
            </button>
            ';
        }
        ?>

    </div>
    <div class="header-module">
        <ul>
            <li>
                <a href="/profile">профіль</a>
            </li>
            <li>
                <a href="/logout">вийти</a>
            </li>
        </ul>
    </div>
</header>