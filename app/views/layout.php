<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? ($settings['site_name'] ?? 'Great10 Streaming')) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? ($settings['seo_description'] ?? 'Watch movies and series online.')) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? ($settings['site_name'] ?? 'Great10')) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc ?? ($settings['seo_description'] ?? 'Watch movies and series online.')) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/og-default.jpg') ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($pageTitle ?? ($settings['site_name'] ?? 'Great10')) ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($pageDesc ?? ($settings['seo_description'] ?? 'Watch movies and series online.')) ?>">
    <meta property="twitter:image" content="<?= htmlspecialchars($pageImage ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/og-default.jpg') ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="apple-touch-icon" href="https://img.icons8.com/color/192/movie-projector.png">

    <!-- Core Styles -->
    <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
    <link rel="icon" href="<?= htmlspecialchars($settings['site_favicon'] ?? '/assets/favicon.ico') ?>">
    
    <!-- Dynamic Header Code -->
    <?= $settings['site_header_code'] ?? '' ?>

</head>
<body>

<header>
    <div class="nav-container">
        <button class="hamburger" onclick="document.querySelector('nav').classList.toggle('active')">☰</button>
        
        <a href="/" class="logo"><?= htmlspecialchars($settings['site_name'] ?? 'Great10') ?></a>
        
        <nav>
            <div style="display: flex; justify-content: flex-end; width: 100%; margin-bottom: 20px;" class="desktop-hidden">
                <button onclick="document.querySelector('nav').classList.remove('active')" style="background:none; border:none; color:white; font-size:1.5rem;">✕</button>
            </div>
            <a href="/">Home</a>
            <a href="/movies">Movies</a>
            <a href="/series">Series</a>
            <a href="/anime">Anime</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="/login" class="desktop-hidden" style="color: var(--primary); margin-top: 10px;">Login</a>
            <?php endif; ?>
        </nav>
        
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="search-container" id="searchContainer">
                <svg class="search-icon" onclick="toggleMobileSearch()" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <div class="mobile-search-wrapper">
                    <input type="text" class="search-input" placeholder="Search movies, series..." id="searchInput">
                    <span class="search-close" onclick="toggleMobileSearch()">✕</span>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-menu" style="position: relative;">
                    <button onclick="document.getElementById('userDropdown').classList.toggle('active')" style="background: none; border: none; color: white; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <img src="<?= $_SESSION['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.$_SESSION['user_username'].'&background=random' ?>" style="width: 32px; height: 32px; border-radius: 50%;">
                    </button>
                    <div id="userDropdown" style="position: absolute; right: 0; top: 120%; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; width: 150px; display: none; z-index: 2002; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                        <a href="/dashboard" style="display: block; padding: 12px; color: var(--text); text-decoration: none; font-size: 0.9rem;">Dashboard</a>
                        <a href="/logout" style="display: block; padding: 12px; color: #f87171; text-decoration: none; border-top: 1px solid var(--border); font-size: 0.9rem;">Logout</a>
                    </div>
                </div>
                <style> #userDropdown.active { display: block !important; } </style>
            <?php else: ?>
                <div style="display: flex; gap: 10px;" class="auth-buttons mobile-hidden">
                    <a href="/login" style="color: var(--text); text-decoration: none; font-weight: 600; font-size: 0.9rem;">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
<style>
    @media (min-width: 769px) { .desktop-hidden { display: none !important; } }
    @media (max-width: 768px) { .mobile-hidden { display: none !important; } }
</style>
<script>
    const searchInput = document.getElementById('searchInput');
    const searchContainer = document.getElementById('searchContainer');
    
    function toggleMobileSearch() {
        if (window.innerWidth <= 768) {
            searchContainer.classList.toggle('mobile-active');
            if (searchContainer.classList.contains('mobile-active')) {
                searchInput.focus();
            }
        }
    }

    const searchResults = document.getElementById('searchResults');
    let debounceTimer;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        clearTimeout(debounceTimer);
        
        if (query.length < 2) {
            searchResults.classList.remove('active');
            return;
        }

        debounceTimer = setTimeout(async () => {
             try {
                 const res = await fetch('/api/search?q=' + encodeURIComponent(query));
                 const data = await res.json();
                 
                 searchResults.innerHTML = '';
                 
                 if (data.results && data.results.length > 0) {
                     data.results.slice(0, 10).forEach(item => {
                         const mediaType = item.media_type;
                         if (mediaType !== 'movie' && mediaType !== 'tv') return;
                         
                         const title = item.title || item.name;
                         const date = (item.release_date || item.first_air_date || '').substring(0,4);
                         const img = item.poster_path ? 'https://image.tmdb.org/t/p/w92' + item.poster_path : 'https://via.placeholder.com/45x68?text=No+Img';
                         
                         const div = document.createElement('a');
                         div.href = `/watch/${item.id}?type=${mediaType}`;
                         div.className = 'search-item';
                         div.innerHTML = `
                            <img src="${img}">
                            <div class="search-info">
                                <span class="search-title">${title}</span>
                                <span class="search-meta">${mediaType.toUpperCase()} • ${date}</span>
                            </div>
                         `;
                         searchResults.appendChild(div);
                     });
                     searchResults.classList.add('active');
                 } else {
                     searchResults.innerHTML = '<div style="padding:15px;text-align:center;color:#94a3b8;">No results found</div>';
                     searchResults.classList.add('active');
                 }
             } catch(e) {}
        }, 300);
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
             searchResults.classList.remove('active');
        }
    });
</script>

<main>
    <?= $content ?>
</main>

<footer>
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between; text-align: left;">
        
        <!-- About / Dynamic Pages -->
        <div style="flex: 1; min-width: 250px;">
            <h3 style="color: var(--text); margin-bottom: 20px;">Great10</h3>
            <p style="margin-bottom: 20px;">The best streaming experience for movies, series, and anime.</p>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php
                    // Directly fetch pages for footer (simple approach)
                    $db_footer = Database::getInstance();
                    $footer_pages = $db_footer->query("SELECT title, slug FROM pages LIMIT 5")->fetchAll();
                ?>
                <?php foreach($footer_pages as $p): ?>
                    <a href="/p/<?= $p['slug'] ?>" style="color: var(--text-muted); text-decoration: none; transition: color 0.2s;"><?= htmlspecialchars($p['title']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="flex: 1; min-width: 300px;">
            <h3 style="color: var(--text); margin-bottom: 20px;">Contact Us</h3>
            <form action="/contact" method="POST" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
                <input type="email" name="email" required placeholder="Your Email" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: white;">
                <textarea name="message" required placeholder="How can we help?" rows="3" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: white;"></textarea>
                <button type="submit" class="btn-play" style="border: none; cursor: pointer; font-size: 0.9rem; padding: 10px 20px;">Send Message</button>
            </form>
        </div>

    </div>
    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem;">
        <?= $settings['site_footer_code'] ?? '' ?>
        <p>&copy; <?= date('Y') ?> Great10. All rights reserved.</p>
    </div>
</footer>

<script>
    // PWA Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(reg => {
                console.log('SW registered:', reg);
            }).catch(err => console.log('SW error:', err));
        });
    }
</script>
</body>
</html>
