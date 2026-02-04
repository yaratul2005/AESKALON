<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0f172a; --sidebar: #1e293b; --primary: #38bdf8; --text: #f8fafc; --text-muted: #94a3b8; --border: #334155; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        aside { width: 260px; background: var(--sidebar); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 20px; }
        .brand { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .nav-item { padding: 12px 15px; margin-bottom: 5px; color: var(--text-muted); text-decoration: none; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .nav-item:hover, .nav-item.active { background: rgba(56, 189, 248, 0.1); color: var(--primary); }
        .nav-footer { margin-top: auto; border-top: 1px solid var(--border); padding-top: 20px; }
        
        /* Main */
        main { flex: 1; padding: 30px; overflow-y: auto; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { margin: 0; font-weight: 600; }
        
        /* Components */
        .card { background: var(--sidebar); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .btn { background: var(--primary); color: #0f172a; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
        input, select, textarea { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 10px; border-radius: 6px; width: 100%; box-sizing: border-box; margin-top: 5px; margin-bottom: 15px; }
        label { color: var(--text-muted); font-size: 0.9rem; font-weight: 500; }
        
        /* Message */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid transparent; }
        .alert-success { background: rgba(74, 222, 128, 0.1); border-color: rgba(74, 222, 128, 0.2); color: #4ade80; }
        .alert-error { background: rgba(248, 113, 113, 0.1); border-color: rgba(248, 113, 113, 0.2); color: #f87171; }

        /* Tabs */
        .tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; color: var(--text-muted); border-bottom: 2px solid transparent; }
        .tab.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>

<aside>
    <div class="brand">
        <span>⚡ Great10</span>
    </div>
    <nav>
        <a href="/admin/dashboard" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">Dashboard</a>
        <a href="/admin/users" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'users') ? 'active' : '' ?>">Users & Bans</a>
        <a href="/admin/settings" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'settings') ? 'active' : '' ?>">Settings</a>
    </nav>
    <div class="nav-footer">
        <a href="/" target="_blank" class="nav-item">View Website</a>
        <a href="/admin/logout" class="nav-item" style="color: #f87171;">Logout</a>
    </div>
</aside>

<main>
    <header>
        <h1><?= $pageTitle ?></h1>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Dynamic Content Include -->
    <?php 
        $route = str_replace('/admin/', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if ($route == 'dashboard') include 'dashboard.php';
        elseif ($route == 'users') include 'users.php';
        elseif ($route == 'settings') include 'settings.php';
    ?>
</main>

<script>
    // Tab Switching Logic
    document.querySelectorAll('.tab').forEach(t => {
        t.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            document.getElementById(t.dataset.target).classList.add('active');
        });
    });
</script>

</body>
</html>
