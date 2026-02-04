

<div class="player-container animate-fade-in">
    <!-- VidLink Player -->
    <div class="iframe-wrapper">
        <?php 
            $src = "https://vidlink.pro/movie/$id";
            if ($type == 'tv') {
                $src = "https://vidlink.pro/tv/$id/$season/$episode";
            }
        ?>
        <iframe src="<?= $src ?>" allowfullscreen></iframe>
    </div>

    <!-- Episode Selector for Series -->
    <?php if ($type == 'tv'): ?>
        <div style="margin-top: 20px;">
            <h3>Seasons</h3>
            <select onchange="window.location.href='?type=tv&s='+this.value" style="padding: 8px; background: #1e293b; color: white; border: 1px solid #334155; border-radius: 4px;">
                <?php foreach($seasons as $s): ?>
                    <option value="<?= $s['season_number'] ?>" <?= $s['season_number'] == $season ? 'selected' : '' ?>>
                        Season <?= $s['season_number'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h3>Episodes</h3>
            <div class="episode-scroller">
                <?php foreach($episodes as $ep): ?>
                    <a href="?type=tv&s=<?= $season ?>&e=<?= $ep['episode_number'] ?>" 
                       class="episode-card <?= $ep['episode_number'] == $episode ? 'active' : '' ?>">
                        <div class="ep-num">Ep <?= $ep['episode_number'] ?></div>
                        <div class="ep-title"><?= htmlspecialchars($ep['name']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="movie-details">
        <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" class="poster-thumb">
        <div class="meta-info">
            <h1><?= htmlspecialchars($title) ?></h1>
            <p class="overview"><?= htmlspecialchars($movie['overview']) ?></p>
        </div>
    </div>
</div>
