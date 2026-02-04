<?php
    // Limit sliders to show only first 12 items on homepage
    // The rest are accessible via "View All"
    $trending = array_slice($trending, 0, 12);
    $series = array_slice($series, 0, 12);
    $anime = array_slice($anime, 0, 12);
?>

<?php 
    $hero = $trending[0] ?? null;
    $heroImg = $hero ? "https://image.tmdb.org/t/p/original" . $hero['backdrop_path'] : '';
?>

<?php if($hero): ?>
<div class="hero-advanced" style="background-image: url('<?= $heroImg ?>');">
    <div class="hero-info animate-fade-in">
        <span class="badge">#1 Trending</span>
        <h1><?= htmlspecialchars($hero['title'] ?? $hero['name']) ?></h1>
        <p><?= htmlspecialchars(substr($hero['overview'], 0, 200)) ?>...</p>
        <a href="/watch/<?= $hero['id'] ?>?type=movie" class="btn-play">Watch Now</a>
    </div>
</div>
<?php endif; ?>

<!-- Trending Movies -->
<div class="section-header">
    <h2 class="section-title"><span>Trending Movies</span></h2>
    <a href="/movies" class="btn-view-all">View All</a>
</div>
<div class="scroller">
    <?php foreach ($trending as $m): ?>
        <a href="/watch/<?= $m['id'] ?>?type=movie" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $m['poster_path'] ?>" class="poster" loading="lazy">
            <span class="title"><?= htmlspecialchars($m['title']) ?></span>
            <div class="meta"><?= substr($m['release_date'] ?? '', 0, 4) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Series -->
<div class="section-header">
    <h2 class="section-title"><span>Popular Series</span></h2>
    <a href="/series" class="btn-view-all">View All</a>
</div>
<div class="scroller">
    <?php foreach ($series as $s): ?>
        <a href="/watch/<?= $s['id'] ?>?type=tv" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $s['poster_path'] ?>" class="poster" loading="lazy">
            <span class="title"><?= htmlspecialchars($s['name']) ?></span>
             <div class="meta"><?= substr($s['first_air_date'] ?? '', 0, 4) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Anime -->
<div class="section-header">
    <h2 class="section-title"><span>Anime</span></h2>
    <a href="/anime" class="btn-view-all">View All</a>
</div>
<div class="scroller">
    <?php foreach ($anime as $a): ?>
        <a href="/watch/<?= $a['id'] ?>?type=tv" class="movie-card">
            <img src="https://image.tmdb.org/t/p/w342<?= $a['poster_path'] ?>" class="poster" loading="lazy">
            <span class="title"><?= htmlspecialchars($a['name']) ?></span>
            <div class="meta"><?= substr($a['first_air_date'] ?? '', 0, 4) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Bottom Spacer -->
<div style="height: 100px;"></div>
