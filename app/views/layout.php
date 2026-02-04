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
    <div class="container">
        <?= $settings['site_footer_code'] ?? '' ?>
    </div>
</footer>

</body>
</html>
