<style>
    .player-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 20px;
    }

    .iframe-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 50px rgba(56, 189, 248, 0.1);
    }

    .iframe-wrapper iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: none;
    }

    .movie-details {
        margin-top: 2rem;
        background: var(--surface);
        padding: 2rem;
        border-radius: 12px;
        display: flex;
        gap: 30px;
    }

    .poster-thumb {
        width: 200px;
        border-radius: 8px;
    }

    .meta-info h1 {
        margin-top: 0;
        color: var(--primary);
    }

    .tagline {
        font-style: italic;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .overview {
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .movie-details { flex-direction: column; }
        .poster-thumb { width: 100%; max-width: 300px; margin: 0 auto; }
    }
</style>

<div class="player-container animate-fade-in">
    <div class="iframe-wrapper">
        <iframe src="https://vidlink.pro/movie/<?= $movie['id'] ?>" allowfullscreen></iframe>
    </div>

    <div class="movie-details">
        <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" class="poster-thumb">
        <div class="meta-info">
            <h1><?= htmlspecialchars($movie['title']) ?></h1>
            <div class="tagline"><?= htmlspecialchars($movie['tagline'] ?? '') ?></div>
            <p class="overview"><?= htmlspecialchars($movie['overview']) ?></p>
            <p><strong>Release Date:</strong> <?= $movie['release_date'] ?></p>
            <p><strong>Rating:</strong> <span style="color: var(--accent);">★ <?= $movie['vote_average'] ?></span></p>
        </div>
    </div>
</div>
