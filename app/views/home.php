<style>
    /* Horizontal Scroller */
    .section-header {
        max-width: 1400px;
        margin: 2rem auto 1rem;
        padding: 0 4%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title span {
        border-bottom: 2px solid var(--accent);
        padding-bottom: 5px;
    }

    .scroller {
        display: flex;
        overflow-x: auto;
        gap: 20px;
        padding: 20px 4%;
        scroll-behavior: smooth;
        scrollbar-width: none; /* Firefox */
    }
    .scroller::-webkit-scrollbar { display: none; }

    .movie-card {
        min-width: 180px;
        flex-shrink: 0;
        transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    .movie-card:hover {
        transform: scale(1.05);
        z-index: 10;
    }

    /* Advanced Hero */
    .hero-advanced {
        height: 70vh;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding-bottom: 50px;
    }
    
    .hero-advanced::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0; height: 50%;
        background: linear-gradient(to top, var(--bg), transparent);
        z-index: 0;
    }

    .hero-info {
        position: relative;
        z-index: 2;
        padding: 0 4%;
        width: 100%;
        max-width: 800px;
    }

    .hero-info h1 {
        font-size: 3.5rem;
        line-height: 1.1;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    @media(max-width: 768px) {
        .hero-advanced { height: 50vh; }
        .hero-info h1 { font-size: 2rem; }
        .movie-card { min-width: 140px; }
    }
</style>

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
