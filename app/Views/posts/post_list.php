<?php if (empty($posts)): ?>
    <p>Немає постів для відображення.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <div class="post-header">
                <div class="post-author">
                    <div class="user-avatar">
                        <?= strtoupper(mb_substr($post['username'], 0, 1)) ?>
                    </div>
                    <span>
                        <?= htmlspecialchars($post['username']) ?> ·
                        <?= date("d.m.Y H:i", strtotime($post['created_at'])) ?>
                    </span>
                </div>
            </div>

            <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            <div class="post-actions">
                <div class="post-votes">
                    <span>⬆️</span>
                    <span><?= $post['views'] ?></span>
                    <span>⬇️</span>
                </div>
                <div class="post-action">
                    <span>💬</span>
                    <span>0 коментарів</span>
                </div>
                <div class="post-action">
                    <span>↗️</span>
                    <span>Поділитися</span>
                </div>
                <div class="post-action">
                    <span>🔖</span>
                    <span>Зберегти</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>