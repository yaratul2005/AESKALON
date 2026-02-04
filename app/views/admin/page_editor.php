<div class="card">
    <form action="/admin/pages/save" method="POST">
        <input type="hidden" name="id" value="<?= $page['id'] ?? '' ?>">
        
        <label>Page Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required placeholder="e.g., Privacy Policy">
        
        <label>Slug (URL) - Leave empty to auto-generate</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" placeholder="e.g., privacy-policy">
        
        <label>Content (HTML Allowed)</label>
        <textarea name="content" style="height: 400px; font-family: monospace;"><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">Save Page</button>
            <a href="/admin/pages" class="btn" style="background: transparent; border: 1px solid var(--border); margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>
