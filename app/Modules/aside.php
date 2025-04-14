<div class="sidebar-wrapper">
    <button id="toggleSidebarBtn" class="toggle-btn">☰</button>
    <aside class="sidebar">

        <div class="sidebar-section">
            <!-- <h3>Категорії</h3> -->
            <?php $filter = $_GET['filter'] ?? 'home'; ?>
            <ul class="community-list">
                <li><a href="/home?filter=home"
                        class="community-item btn <?= $filter === 'home' ? 'cornered active' : '' ?>"><p class="in-btn">HOME</p></a></li>
                <li><a href="/home?filter=popular"
                        class="community-item btn <?= $filter === 'popular' ? 'cornered active' : '' ?>"><p class="in-btn">POPULAR</p></a></li>
                <li><a href="/home?filter=latest"
                        class="community-item btn <?= $filter === 'latest' ? 'cornered active' : '' ?>"><p class="in-btn">LATEST</p></a></li>
                <li><a href="/channels"
                        class="community-item btn  <?= $_SERVER['REQUEST_URI'] === '/channels' ? 'cornered active' : '' ?>"><p class="in-btn">COMMUNITY</p></a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3>Мої спільноти</h3>

            <button class="btn" onclick="showCreateChannel()">Створити спільноту</button>

            <ul class="community-list">
                <?php foreach ($communities as $community): ?>
                    <li class="community-item<?= ($community['id'] == $activeCommunityId ? ' active' : '') ?>">
                        <div class="community-icon"><?= htmlspecialchars(mb_substr($community['name'], 0, 1)) ?></div>
                        <span><?= htmlspecialchars($community['name']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </aside>
</div>