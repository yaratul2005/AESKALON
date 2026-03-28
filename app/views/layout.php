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
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
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
            <div class="search-trigger">
                <button onclick="document.getElementById('fullSearchOverlay').classList.add('active'); document.getElementById('fullSearchInput').focus();" style="background:none; border:none; color:var(--text); cursor:pointer; display: flex; align-items: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
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
    // Legacy inline search script removed, migrated to overlay script at bottom of body
</script>

<!-- Full Screen Search Overlay -->
<div id="fullSearchOverlay" class="full-search-overlay">
    <button class="fs-close" onclick="document.getElementById('fullSearchOverlay').classList.remove('active')">&times;</button>
    <div class="fs-search-container">
        <input type="text" id="fullSearchInput" class="fs-search-input" placeholder="What do you want to watch?">
        <div id="fsSearchResults" class="fs-search-results"></div>
    </div>
</div>

<style>
.full-search-overlay { position: fixed; inset: 0; background: rgba(8,10,15,0.95); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); z-index: 99999; display: flex; align-items: flex-start; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.4s ease; padding-top: 15vh; }
.full-search-overlay.active { opacity: 1; visibility: visible; }
.fs-close { position: absolute; top: 30px; right: 40px; background: none; border: none; font-size: 3rem; color: var(--text-muted); cursor: pointer; transition: color 0.2s; }
.fs-close:hover { color: var(--accent); }
.fs-search-container { width: 90%; max-width: 900px; display: flex; flex-direction: column; gap: 40px; }
.fs-search-input { width: 100%; font-size: clamp(2rem, 5vw, 4rem); font-family: 'Bebas Neue', sans-serif; background: transparent; border: none; border-bottom: 2px solid rgba(255,255,255,0.2); color: white; padding-bottom: 10px; transition: border-color 0.3s; box-shadow: none; outline: none; }
.fs-search-input:focus { border-color: var(--primary); }
.fs-search-results { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 20px; max-height: 60vh; overflow-y: auto; scrollbar-width: none; }
.fs-search-results::-webkit-scrollbar { display: none; }
.fs-result-card { display: flex; flex-direction: column; gap: 8px; text-decoration: none; transition: transform 0.2s; }
.fs-result-card:hover { transform: scale(1.05); }
.fs-result-poster { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
.fs-result-title { font-size: 0.9rem; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
.fs-result-meta { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }
</style>

<script>
    const fsInput = document.getElementById('fullSearchInput');
    const fsResults = document.getElementById('fsSearchResults');
    let fsDebounceTimer;

    fsInput.addEventListener('input', (e) => {
        const query = e.target.value;
        clearTimeout(fsDebounceTimer);
        
        if (query.length < 2) {
            fsResults.innerHTML = ''; return;
        }

        fsResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--primary); padding: 50px;">Searching database...</div>';

        fsDebounceTimer = setTimeout(async () => {
             try {
                 const res = await fetch('/api/search?q=' + encodeURIComponent(query));
                 const data = await res.json();
                 
                 fsResults.innerHTML = '';
                 
                 if (data.results && data.results.length > 0) {
                     data.results.forEach(item => {
                         const mediaType = item.media_type;
                         if (mediaType !== 'movie' && mediaType !== 'tv') return;
                         
                         const title = item.title || item.name;
                         const date = (item.release_date || item.first_air_date || '').substring(0,4);
                         const img = item.poster_path ? 'https://image.tmdb.org/t/p/w200' + item.poster_path : 'https://placehold.co/200x300?text=No+Img';
                         
                         const div = document.createElement('a');
                         div.href = `/watch/${item.id}?type=${mediaType}`;
                         div.className = 'fs-result-card animate-fade-in';
                         div.innerHTML = `
                            <img src="${img}" class="fs-result-poster">
                            <span class="fs-result-title">${title}</span>
                            <span class="fs-result-meta">${mediaType} • ${date}</span>
                         `;
                         fsResults.appendChild(div);
                     });
                 } else {
                     fsResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 50px; font-size: 1.5rem;">No results found.</div>';
                 }
             } catch(e) {
                 fsResults.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--accent); padding: 50px;">Error searching</div>';
             }
        }, 400); // 400ms Debounce requested
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') document.getElementById('fullSearchOverlay').classList.remove('active');
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

    // Global Trailer Modal Logic
    function openTrailer(youtubeKey) {
        const modal = document.getElementById('trailerModal');
        const iframe = document.getElementById('trailerIframe');
        iframe.src = 'https://www.youtube.com/embed/' + youtubeKey + '?autoplay=1&rel=0&modestbranding=1';
        modal.style.display = 'flex';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    
    function closeTrailer() {
        const modal = document.getElementById('trailerModal');
        const iframe = document.getElementById('trailerIframe');
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            iframe.src = '';
        }, 300);
    }
</script>

<!-- Trailer Overlay -->
<div id="trailerModal" style="display: none; opacity: 0; transition: opacity 0.3s ease; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(8, 10, 15, 0.95); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(10px);">
    <div style="position: relative; width: 90%; max-width: 1000px; aspect-ratio: 16/9; background: #000; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.8);">
        <button onclick="closeTrailer()" style="position: absolute; top: 15px; right: 20px; background: rgba(0,0,0,0.5); border: none; color: white; font-size: 2rem; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; z-index: 10000; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
        <iframe id="trailerIframe" style="width: 100%; height: 100%; border: none;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>

</body>
</html>
