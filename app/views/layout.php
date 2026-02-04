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
            <a href="/movies">Movies</a>
            <a href="/series">Series</a>
            <a href="/anime">Anime</a>
        </nav>
        
        <div class="search-container">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="search-input" placeholder="Search..." id="searchInput">
            <div class="search-results" id="searchResults"></div>
        </div>

        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-menu" style="position: relative; margin-left: 20px;">
                <button onclick="document.getElementById('userDropdown').classList.toggle('active')" style="background: none; border: none; color: white; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                    <img src="<?= $_SESSION['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.$_SESSION['user_username'].'&background=random' ?>" style="width: 32px; height: 32px; border-radius: 50%;">
                </button>
                <div id="userDropdown" style="position: absolute; right: 0; top: 100%; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; width: 150px; display: none; margin-top: 10px;">
                    <a href="/dashboard" style="display: block; padding: 10px; color: var(--text); text-decoration: none;">Dashboard</a>
                    <a href="/logout" style="display: block; padding: 10px; color: #f87171; text-decoration: none; border-top: 1px solid var(--border);">Logout</a>
                </div>
            </div>
            <style> #userDropdown.active { display: block !important; } </style>
        <?php else: ?>
            <div style="margin-left: 20px; display: flex; gap: 10px;">
                <a href="/login" style="color: var(--text); text-decoration: none; font-weight: 600;">Login</a>
                <a href="/register" class="btn" style="padding: 5px 15px; font-size: 0.9rem;">Sign Up</a>
            </div>
        <?php endif; ?>

    </div>
</header>
<script>
    const searchInput = document.getElementById('searchInput');
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

</body>
</html>
