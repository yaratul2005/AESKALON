<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Core Styles -->
    <link rel="stylesheet" href="/assets/style.css">
    
    <!-- Dynamic Header Code -->
    <?= $settings['site_header_code'] ?? '' ?>

</head>
<body>

<header>
    <div class="nav-container">
        <a href="/" class="logo"><?= htmlspecialchars($settings['site_name'] ?? 'Great10') ?></a>
        <nav>
            <a href="/">Home</a>
            <a href="#">Movies</a>
            <a href="#">Series</a>
        </nav>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<footer>
    <div class="container">
        <?= $settings['site_footer_code'] ?? '' ?>
    </div>
</footer>

</body>
</html>
