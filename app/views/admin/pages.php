<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Custom Pages (Footer)</h3>
        <a href="/admin/pages/new" class="btn">+ New Page</a>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="color: var(--text-muted); border-bottom: 1px solid var(--border);">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Slug</th>
                <th style="padding: 10px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $p): ?>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 10px;">#<?= $p['id'] ?></td>
                <td style="padding: 10px; font-weight: 500;"><?= htmlspecialchars($p['title']) ?></td>
                <td style="padding: 10px;">/p/<?= htmlspecialchars($p['slug']) ?></td>
                <td style="padding: 10px;">
                    <a href="/admin/pages/edit/<?= $p['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                    <a href="/admin/pages/delete/<?= $p['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #f87171; margin-left: 5px;" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
