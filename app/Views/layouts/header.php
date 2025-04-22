<?php

$username = $_SESSION['user']['username'] ?? null;

?>
<header>
    <button id="toggleSidebarBtnMin" class="toggle-btn-min">☰</button>
    <div class="logo"><a href="/home">Pingora</a></div>
    <div class="search-bar">
        <input type="text" placeholder="Search...">
    </div>
    <div class="header-actions">
        <a href="/posts/create" class="btn">
            <p>>CREATE-POST()</p>
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
        <div class="header-module-in"></div>
        <ul>
            <li>
                <a href="/profile" class="btn"><p>- profile ></p></a>
            </li>
            <li>
                <a href="/logout" class="btn"><p>- loguot ></p></a>
            </li>
        </ul>
    </div>
</header>