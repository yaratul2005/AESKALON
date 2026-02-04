<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin.css">
</head>
<body>

<aside>
    <div class="brand">
        <span>⚡ Great10</span>
    </div>
    <nav>
        <a href="/admin/dashboard" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">Dashboard</a>
        <a href="/admin/users" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'users') ? 'active' : '' ?>">Users & Bans</a>
        <a href="/admin/pages" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], 'pages') ? 'active' : '' ?>">Pages (CMS)</a>
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
        elseif ($route == 'dashboard') include 'dashboard.php';
        elseif ($route == 'users') include 'users.php';
        elseif ($route == 'settings') include 'settings.php';
        elseif ($route == 'pages') include 'pages.php';
        elseif (strpos($route, 'pages/') !== false) include 'page_editor.php';
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
