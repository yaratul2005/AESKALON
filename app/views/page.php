<div class="container" style="max-width: 800px; margin: 4rem auto; padding: 0 20px;">
    <h1 style="border-bottom: 2px solid var(--border); padding-bottom: 20px; margin-bottom: 30px;"><?= htmlspecialchars($page['title']) ?></h1>
    <div class="page-content" style="line-height: 1.8; color: #cbd5e1;">
        <?= $page['content'] // RAW HTML is allowed for CMS pages ?>
    </div>
</div>
