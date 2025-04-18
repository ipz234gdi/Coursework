<!-- Views/channels/show.php -->

<div class="channel-header">
    <img src="<?= htmlspecialchars($channel['banner_url'] ?? '/img/default_banner.jpg') ?>" alt=""
        class="channel-banner" />
    <div class="channel-info">
        <img class="channel-icon" src="<?= htmlspecialchars($channel['icon_url'] ?? '/img/default_icon.png') ?>"
            alt="<?= htmlspecialchars($channel['name']) ?>">
        <h1>p/<?= htmlspecialchars($channel['name']) ?></h1>
        <p class="channel-desc"><?= nl2br(htmlspecialchars($channel['description'])) ?></p>
        <div class="channel-controls">
            <?php if ($isJoined): ?>
                <button class="btn btn-joined">Joined</button>
            <?php else: ?>
                <form method="POST" action="/channels/<?= $channel['id'] ?>/join">
                    <button class="btn btn-join">Join</button>
                </form>
            <?php endif; ?>
            <span class="member-count"><?= $memberCount ?> members</span>
        </div>
    </div>
</div>

<section class="community-highlights">
    <h2>Community highlights</h2>
    <div class="highlights-grid">
        <?php foreach ($highlights as $h): ?>
            <a href="/posts/<?= $h['id'] ?>" class="highlight-card">
                <?php if ($h['media_url']): ?>
                    <img src="<?= htmlspecialchars($h['media_url']) ?>" alt="">
                <?php else: ?>
                    <div class="no-image"></div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($h['title']) ?></h3>
                <small><?= $h['views'] ?> views</small>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="channel-posts">
    <?php require __DIR__ . '/../posts/post_list.php'; ?>
</section>