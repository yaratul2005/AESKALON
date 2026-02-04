

<?php 
    $hero = $trending[0] ?? null;
    $heroImg = $hero ? "https://image.tmdb.org/t/p/original" . $hero['backdrop_path'] : '';
?>

<?php if($hero): ?>
<div class="hero-advanced" style="background: url('<?= $heroImg ?>') no-repeat center center/cover;">
    <div class="hero-info animate-fade-in">
        <span class="badge" style="background: var(--primary); color: #000; padding: 5px 10px; font-weight: bold; border-radius: 4px;">#1 Trending</span>
        <h1><?= htmlspecialchars($hero['title'] ?? $hero['name']) ?></h1>
        <p style="color: #cbd5e1; font-size: 1.1rem; line-height: 1.6;"><?= substr($hero['overview'], 0, 200) ?>...</p>
        <div style="margin-top: 20px;">
            <a href="/watch/<?= $hero['id'] ?>?type=movie" class="btn-play">Watch Now</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Trending Movies -->
<div class="section-header">
    <h2 class="section-title"><span>Trending Movies</span></h2>
</div>
<div class="scroller">
    <?php foreach ($trending as $m): ?>
        <a href="/watch/<?= $m['id'] ?>?type=movie" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $m['poster_path'] ?>" class="poster" loading="lazy">
            <div class="mt-2 text-sm font-semibold truncate"><?= htmlspecialchars($m['title']) ?></div>
            <div class="text-xs text-gray-400"><?= substr($m['release_date'] ?? '', 0, 4) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Series -->
<div class="section-header">
    <h2 class="section-title"><span>Popular Series</span></h2>
</div>
<div class="scroller">
    <?php foreach ($series as $s): ?>
        <a href="/watch/<?= $s['id'] ?>?type=tv" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $s['poster_path'] ?>" class="poster" loading="lazy">
            <div class="mt-2 text-sm font-semibold truncate"><?= htmlspecialchars($s['name']) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Anime -->
<div class="section-header">
    <h2 class="section-title"><span>Anime</span></h2>
</div>
<div class="scroller">
    <?php foreach ($anime as $a): ?>
        <a href="/watch/<?= $a['id'] ?>?type=tv" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $a['poster_path'] ?>" class="poster" loading="lazy">
            <div class="mt-2 text-sm font-semibold truncate"><?= htmlspecialchars($a['name']) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Infinite Scroll Container -->
<div class="section-header">
    <h2 class="section-title"><span>Discover More</span></h2>
</div>
<div class="grid" id="infinite-grid"></div>
<div id="loading" style="text-align: center; padding: 20px; display: none;">Loading...</div>

<script>
    // Simple Infinite Scroll
    let page = 1;
    const grid = document.getElementById('infinite-grid');
    const loading = document.getElementById('loading');

    // Initial Load
    loadMore();

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
            loadMore();
        }
    });

    async function loadMore() {
        if (loading.style.display === 'block') return;
        loading.style.display = 'block';
        
        try {
            const res = await fetch(`/browse?page=${page}`);
            const data = await res.json();
            
            data.results.forEach(m => {
                if (!m.poster_path) return;
                const div = document.createElement('a');
                div.href = `/watch/${m.id}?type=movie`;
                div.className = 'movie-card animate-fade-in';
                div.innerHTML = `
                    <img src="https://image.tmdb.org/t/p/w342${m.poster_path}" class="poster" loading="lazy">
                    <div class="info">
                        <span class="title">${m.title || m.name}</span>
                    </div>
                `;
                grid.appendChild(div);
            });
            page++;
        } catch(e) {}
        loading.style.display = 'none';
    }
</script>
