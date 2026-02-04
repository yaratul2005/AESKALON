<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Great10</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid #334155; padding-bottom: 1rem; }
        h1 { color: #38bdf8; margin: 0; }
        .card { background: #1e293b; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #334155; }
        h3 { margin-top: 0; color: #94a3b8; border-bottom: 1px solid #334155; padding-bottom: 0.5rem; }
        label { display: block; margin-bottom: 0.5rem; margin-top: 1rem; font-weight: 500; }
        input[type="text"], textarea { width: 100%; padding: 0.75rem; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; box-sizing: border-box; }
        textarea { min-height: 100px; font-family: monospace; }
        button { background: #38bdf8; color: #0f172a; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #064e3b; color: #6ee7b7; border: 1px solid #059669; }
        .alert-error { background: #450a0a; color: #fca5a5; border: 1px solid #dc2626; }
        .update-list { list-style: none; padding: 0; }
        .update-list li { background: #334155; padding: 0.5rem; margin-bottom: 0.5rem; border-radius: 4px; display: flex; justify-content: space-between; }
        .badge { background: #f59e0b; color: #000; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard</h1>
            <a href="/" target="_blank" style="color: #38bdf8; text-decoration: none;">View Site &rarr;</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- UPDATER SECTION -->
        <div class="card">
            <h3>System Updates</h3>
            <?php if (empty($pendingUpdates)): ?>
                <p style="color: #4ade80;">System is up to date.</p>
            <?php else: ?>
                <p>Pending Updates found in <code>/updates</code> folder:</p>
                <ul class="update-list">
                    <?php foreach ($pendingUpdates as $file): ?>
                        <li><?= htmlspecialchars($file) ?> <span class="badge">PENDING</span></li>
                    <?php endforeach; ?>
                </ul>
                <form action="/admin/run-updates" method="POST">
                    <button type="submit" style="background: #f59e0b;">Run Database Updates</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- SETTINGS SECTION -->
        <div class="card">
            <h3>General Settings</h3>
            <form action="/admin/update" method="POST">
                <label>Site Name</label>
                <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">

                <label>SEO Description</label>
                <textarea name="seo_description"><?= htmlspecialchars($settings['seo_description'] ?? '') ?></textarea>

                <label>Custom Header HTML (CSS/Analytics)</label>
                <textarea name="site_header_code"><?= htmlspecialchars($settings['site_header_code'] ?? '') ?></textarea>

                <label>Custom Footer HTML (JS/Copyright)</label>
                <textarea name="site_footer_code"><?= htmlspecialchars($settings['site_footer_code'] ?? '') ?></textarea>

                <br><br>
                <button type="submit">Save Settings</button>
            </form>
        </div>
    </div>
</body>
</html>
