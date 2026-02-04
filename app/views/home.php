<style>
    /* Hero Section */
    .hero {
        position: relative;
        height: 60vh;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    
    .hero-bg {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-size: cover;
        background-position: center;
        filter: blur(4px) brightness(0.4);
        z-index: -1;
        transition: background-image 0.5s ease-in-out;
    }

    .hero-content {
        max-width: 800px;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .cta-btn {
        display: inline-block;
        padding: 12px 30px;
        background: var(--primary);
        color: #0f172a;
        font-weight: 800;
        text-decoration: none;
        border-radius: 30px;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
    }

    .cta-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(56, 189, 248, 0.6);
    }

    /* Movies Grid */
    .section-title {
        max-width: 1200px;
        margin: 2rem auto 1rem;
        padding: 0 20px;
        font-size: 1.5rem;
        border-left: 4px solid var(--accent);
        padding-left: 10px;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .movie-card {
        background: var(--surface);
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        cursor: pointer;
        display: block; /* For anchor tag */
        text-decoration: none;
    }

    .movie-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .poster {
        width: 100%;
        aspect-ratio: 2/3;
        object-fit: cover;
        background: #334155;
    }

    .info {
        padding: 15px;
    }

    .title {
        color: var(--text);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .meta {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 5px;
        display: flex;
        justify-content: space-between;
    }

    .rating {
        color: var(--accent);
        font-weight: bold;
    }
</style>

<?php 
    $heroMovie = $movies[0] ?? null; 
    $heroImage = $heroMovie ? "https://image.tmdb.org/t/p/original" . $heroMovie['backdrop_path'] : '';
?>

<?php if ($heroMovie): ?>
<div class="hero">
    <div class="hero-bg" style="background-image: url('<?= $heroImage ?>');"></div>
    <div class="hero-content animate-fade-in">
        <h1><?= htmlspecialchars($heroMovie['title']) ?></h1>
        <p><?= htmlspecialchars(substr($heroMovie['overview'], 0, 150)) ?>...</p>
        <a href="/watch/<?= $heroMovie['id'] ?>" class="cta-btn">Watch Now</a>
    </div>
</div>
<?php endif; ?>

<h2 class="section-title">Trending Now</h2>
<div class="grid">
    <?php foreach ($movies as $movie): ?>
        <a href="/watch/<?= $movie['id'] ?>" class="movie-card animate-fade-in">
            <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" loading="lazy" class="poster" alt="<?= htmlspecialchars($movie['title']) ?>">
            <div class="info">
                <span class="title"><?= htmlspecialchars($movie['title']) ?></span>
                <div class="meta">
                    <span><?= substr($movie['release_date'], 0, 4) ?></span>
                    <span class="rating">★ <?= $movie['vote_average'] ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
