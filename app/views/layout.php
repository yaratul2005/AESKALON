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
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --primary: #38bdf8;
            --accent: #f472b6;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --glass: rgba(30, 41, 59, 0.7);
        }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* Glassmorphism Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        nav a {
            color: var(--text);
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: var(--primary);
        }

        /* Main Content */
        main {
            padding-top: 80px; /* Space for fixed header */
            min-height: 80vh;
        }

        /* Footer */
        footer {
            background: var(--surface);
            padding: 2rem 0;
            text-align: center;
            margin-top: 4rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Global Animations */
         @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

    </style>
    
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
