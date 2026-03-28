<?php
$bgImage = "https://image.tmdb.org/t/p/original" . ($movie['backdrop_path'] ?? '');
$posterImage = "https://image.tmdb.org/t/p/w500" . ($movie['poster_path'] ?? '');
$isSeries = ($type == 'tv');
$runtime = $isSeries ? ($movie['episode_run_time'][0] ?? 0) : ($movie['runtime'] ?? 0);
$rating = number_format($movie['vote_average'] ?? 0, 1);
$year = substr($movie['release_date'] ?? $movie['first_air_date'] ?? '', 0, 4);
$genres = array_slice($movie['genres'] ?? [], 0, 3);
$trailerKey = '';
if (isset($movie['videos']['results'])) {
    foreach($movie['videos']['results'] as $v) {
        if ($v['site'] == 'YouTube' && ($v['type'] == 'Trailer' || $v['type'] == 'Teaser')) { $trailerKey = $v['key']; break; }
    }
}
$cast = array_slice($movie['credits']['cast'] ?? [], 0, 10);
$keywords = ($isSeries) ? ($movie['keywords']['results'] ?? []) : ($movie['keywords']['keywords'] ?? []);
$keywords = array_slice($keywords, 0, 8);
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "<?= $isSeries ? 'TVSeries' : 'Movie' ?>",
  "name": "<?= htmlspecialchars($title) ?>",
  "image": "https://image.tmdb.org/t/p/w1280<?= $movie['backdrop_path'] ?? $movie['poster_path'] ?? '' ?>",
  "description": "<?= htmlspecialchars($movie['overview'] ?? '') ?>",
  "datePublished": "<?= htmlspecialchars($movie['release_date'] ?? $movie['first_air_date'] ?? '') ?>",
  "aggregateRating": { "@type": "AggregateRating", "ratingValue": "<?= $movie['vote_average'] ?? '0' ?>", "bestRating": "10", "ratingCount": "<?= $movie['vote_count'] ?? '1' ?>" }
}
</script>

<div class="watch-header">
    <div class="backdrop" style="background-image: url('<?= $bgImage ?>');"></div>
    <div class="header-content animate-fade-in">
        <div class="poster-3d-wrapper"><img src="<?= $posterImage ?>" class="poster-3d"></div>
        <div class="header-info">
            <h1><?= htmlspecialchars($title) ?></h1>
            <div class="ribbon">
                <span class="ribbon-item badge-hd">4K Ultra HD</span>
                <span class="ribbon-item">★ <?= $rating ?> TMDB</span>
                <span class="ribbon-item" style="color: var(--primary); font-weight: bold; font-family: 'Bebas Neue', sans-serif; font-size: 1.1rem; letter-spacing: 1px;">★ <?= $userRating ?> User Rating (<?= $userVotes ?> votes)</span>
                <span class="ribbon-item"><?= $year ?></span>
                <?php if($runtime): ?><span class="ribbon-item"><?= floor($runtime/60).'h '.($runtime%60).'m' ?></span><?php endif; ?>
                <div class="genre-pills"><?php foreach($genres as $g): ?><span class="genre"><?= $g['name'] ?></span><?php endforeach; ?></div>
            </div>
            
            <div class="interactive-rating">
                <span style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.6);margin-bottom:5px;display:block;">Rate this Title</span>
                <div class="stars" id="starContainer">
                    <?php for($i=1; $i<=10; $i++): ?><span class="star" data-val="<?= $i ?>">★</span><?php endfor; ?>
                </div>
            </div>
            <p class="overview"><?= htmlspecialchars($movie['overview'] ?? '') ?></p>
            <div class="action-cluster">
                <button onclick="document.getElementById('playerSection').scrollIntoView({behavior: 'smooth'})" class="btn-primary-glow">▶ Watch Now</button>
                <?php if($trailerKey): ?><button onclick="openTrailer('<?= $trailerKey ?>')" class="btn-ghost">🎬 Trailer</button><?php endif; ?>
                <button id="btnWatchLater" class="btn-ghost">＋ Watchlist</button>
            </div>
        </div>
    </div>
</div>

<div class="watch-layout">
    <!-- Left Column (70%) -->
    <div class="main-col">
        <div id="playerSection" class="player-mount">
            <div class="server-tabs">
                <?php foreach($servers as $index => $srv): ?>
                    <button class="server-tab <?= $index==0 ? 'active' : '' ?>" onclick="switchServer('<?= $srv['url'] ?>', this)"><?= $srv['name'] ?></button>
                <?php endforeach; ?>
                <div style="margin-left:auto;"><button class="btn-report" onclick="reportStream()">🚩 Report</button></div>
            </div>
            <div class="iframe-container" id="iframeContainer"><iframe id="mainPlayer" src="<?= $servers[0]['url'] ?>" allowfullscreen></iframe></div>
            <div class="runtime-estimator">🕒 Estimated finish time: <span id="finishTime">--:--</span></div>
        </div>
        
        <div class="social-strip">
            <span>Share:</span>
            <a href="https://api.whatsapp.com/send?text=Watch <?= urlencode($title) ?>: <?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" class="social-btn">📱 WhatsApp</a>
            <a href="https://t.me/share/url?url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" class="social-btn">✈️ Telegram</a>
            <a href="#" onclick="navigator.clipboard.writeText(window.location.href); alert('Copied!');" class="social-btn">🔗 Copy Link</a>
        </div>

        <?php if(!empty($cast)): ?>
        <h3 class="section-heading">Top Cast</h3>
        <div class="cast-scroller">
            <?php foreach($cast as $actor): $img = $actor['profile_path'] ? "https://image.tmdb.org/t/p/w185".$actor['profile_path'] : "https://ui-avatars.com/api/?name=".urlencode($actor['name']); ?>
            <div class="cast-card">
                <img src="<?= $img ?>">
                <span class="actor-name"><?= htmlspecialchars($actor['name']) ?></span>
                <span class="actor-char"><?= htmlspecialchars($actor['character']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($keywords)): ?>
        <h3 class="section-heading">Tags</h3>
        <div class="tag-cloud">
            <?php foreach($keywords as $k): ?>
                <a href="/search?q=<?= urlencode($k['name']) ?>" class="tag"><?= $k['name'] ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Column (30%) TV Only -->
    <?php if($isSeries): ?>
    <div class="side-col">
        <div class="tv-panel">
            <h3 style="margin:0 0 15px;">Episodes</h3>
            <div class="season-scroller">
                <?php foreach($seasons as $s): ?>
                    <a href="?type=tv&s=<?= $s['season_number'] ?>" class="season-pill <?= $s['season_number'] == $season ? 'active' : '' ?>">S<?= $s['season_number'] ?> <span>(<?= $s['episode_count'] ?>)</span></a>
                <?php endforeach; ?>
            </div>
            <div class="episode-list" id="epList">
                <?php foreach($episodes as $ep): 
                    $epImg = $ep['still_path'] ? "https://image.tmdb.org/t/p/w300".$ep['still_path'] : "https://placehold.co/300x170?text=Ep+".$ep['episode_number'];
                ?>
                <a href="?type=tv&s=<?= $season ?>&e=<?= $ep['episode_number'] ?>" class="ep-row <?= ($ep['episode_number'] == $episode) ? 'active' : '' ?>" data-code="prog_<?= $season ?>_<?= $ep['episode_number'] ?>">
                    <div class="ep-thumb"><img src="<?= $epImg ?>"><div class="ep-progress" id="prog_<?= $season ?>_<?= $ep['episode_number'] ?>"></div></div>
                    <div class="ep-info">
                        <div class="ep-top"><span class="ep-num">E<?= $ep['episode_number'] ?></span><span class="ep-dur"><?= $ep['runtime'] ?? '24' ?>m</span></div>
                        <div class="ep-title"><?= htmlspecialchars($ep['name']) ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.player-container { display: none; } 
.watch-header { position: relative; width: 100vw; margin-left: calc(-50vw + 50%); height: 75vh; min-height: 500px; display: flex; align-items: center; background: var(--bg); overflow: hidden; }
.backdrop { position: absolute; inset: 0; background-size: cover; background-position: center 20%; z-index: 1; mask-image: linear-gradient(to right, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, rgba(0,0,0,0) 100%); -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, rgba(0,0,0,0) 100%); opacity: 0.6; }
.watch-header::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, var(--bg) 0%, transparent 100%); z-index: 2; pointer-events: none;}
.header-content { position: relative; z-index: 3; display: flex; gap: 50px; align-items: center; max-width: 1400px; margin: 0 auto; padding: 0 4%; width: 100%; }
.poster-3d-wrapper { perspective: 1000px; width: 300px; flex-shrink: 0; }
.poster-3d { width: 100%; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); transition: transform 0.4s; transform: rotateY(15deg) rotateX(5deg); border: 1px solid rgba(255,255,255,0.1); }
.poster-3d-wrapper:hover .poster-3d { transform: rotateY(0deg) rotateX(0deg) scale(1.05); }
.header-info h1 { font-size: clamp(3rem, 5vw, 4.5rem); margin: 0 0 15px; color: white; }
.ribbon { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; margin-bottom: 25px; color: var(--text-muted); font-weight: 500;}
.badge-hd { border: 1px solid var(--text-muted); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; }
.genre-pills { display: flex; gap: 8px; }
.genre { background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; color: white; }
.overview { font-size: 1.15rem; line-height: 1.6; color: rgba(255,255,255,0.8); max-width: 800px; margin-bottom: 35px; }
.action-cluster { display: flex; gap: 15px; flex-wrap: wrap; }
.btn-primary-glow { background: var(--primary); color: #000; font-family: 'Bebas Neue', sans-serif; font-size: 1.4rem; padding: 12px 35px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 0 20px var(--primary-glow); transition: all 0.3s; }
.btn-primary-glow:hover { transform: translateY(-3px); }
.btn-ghost { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-ghost:hover { background: rgba(255,255,255,0.2); border-color: white; }

/* Layout & PIP */
.watch-layout { display: flex; gap: 30px; max-width: 1400px; margin: 0 auto; padding: 40px 20px; }
.main-col { flex: 7; min-width: 0; }
.side-col { flex: 3; min-width: 300px; }
.player-mount { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); transition: all 0.4s; }
.player-mount.pip-mode { position: fixed; bottom: 20px; right: 20px; width: 350px; z-index: 10000; box-shadow: 0 20px 50px rgba(0,0,0,0.9); }
.player-mount.pip-mode .server-tabs, .player-mount.pip-mode .runtime-estimator { display: none; }
.server-tabs { display: flex; background: rgba(0,0,0,0.4); padding: 10px; gap: 10px; border-bottom: 1px solid var(--border); overflow-x: auto; scrollbar-width: none; }
.server-tab { background: transparent; color: var(--text-muted); border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.2s; white-space: nowrap; }
.server-tab.active { background: var(--primary); color: #000; }
.btn-report { background: rgba(244, 63, 94, 0.1); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: bold; transition: all 0.2s; }
.btn-report:hover { background: #f43f5e; color: white; }
.interactive-rating { margin-bottom: 25px; }
.stars { display: flex; gap: 4px; font-size: 1.8rem; cursor: pointer; color: rgba(255,255,255,0.2); transition: color 0.2s; }
.star { transition: color 0.1s; }
.iframe-container { position: relative; padding-bottom: 56.25%; background: #000; }
.iframe-container iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: none; }
.runtime-estimator { padding: 15px; text-align: right; font-size: 0.9rem; color: var(--text-muted); border-top: 1px solid var(--border); background: rgba(0,0,0,0.2); }

/* Components */
.social-strip { display: flex; gap: 10px; margin-bottom: 40px; align-items: center; color: var(--text-muted); font-size: 0.9rem; }
.social-btn { background: var(--surface); border: 1px solid var(--border); padding: 6px 12px; border-radius: 20px; color: white; text-decoration: none; font-weight: 600; transition: all 0.2s; }
.social-btn:hover { background: rgba(255,255,255,0.1); border-color: var(--primary); }
.section-heading { font-family: 'Bebas Neue', sans-serif; font-size: 2rem; margin: 0 0 20px; letter-spacing: 1px; color: white; border-left: 4px solid var(--accent); padding-left: 10px; }
.cast-scroller { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px; scrollbar-width: none; margin-bottom: 40px; }
.cast-scroller::-webkit-scrollbar { display: none; }
.cast-card { display: flex; flex-direction: column; align-items: center; width: 100px; flex-shrink: 0; text-align: center; }
.cast-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 2px solid var(--border); transition: border-color 0.2s; }
.cast-card:hover img { border-color: var(--primary); }
.actor-name { font-size: 0.85rem; font-weight: 600; color: white; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
.actor-char { font-size: 0.75rem; color: var(--text-muted); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
.tag-cloud { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 40px; }
.tag { background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 6px 12px; border-radius: 4px; border: 1px solid var(--border); font-size: 0.85rem; transition: all 0.2s; text-decoration: none; }
.tag:hover { background: var(--primary); color: #000; border-color: var(--primary); }

/* Right Panel Episodes */
.tv-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; position: sticky; top: 100px; max-height: calc(100vh - 120px); display: flex; flex-direction: column; }
.season-scroller { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 15px; border-bottom: 1px solid var(--border); scrollbar-width: none; }
.season-scroller::-webkit-scrollbar { display: none; }
.season-pill { background: rgba(255,255,255,0.05); color: var(--text-muted); font-weight: 600; padding: 6px 16px; border-radius: 20px; white-space: nowrap; text-decoration: none; border: 1px solid transparent; transition: all 0.2s; }
.season-pill.active { background: rgba(232, 184, 75, 0.1); color: var(--accent); border-color: var(--accent); }
.episode-list { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; padding-right: 5px; scrollbar-width: thin; scrollbar-color: #334155 transparent; }
.episode-list::-webkit-scrollbar { width: 6px; }
.episode-list::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
.ep-row { display: flex; gap: 15px; padding: 10px; border-radius: 8px; text-decoration: none; transition: all 0.2s; border: 1px solid transparent; background: rgba(255,255,255,0.02); }
.ep-row:hover { background: rgba(255,255,255,0.05); }
.ep-row.active { border-color: var(--primary); background: rgba(14, 165, 233, 0.05); }
.ep-thumb { width: 100px; aspect-ratio: 16/9; position: relative; border-radius: 6px; overflow: hidden; flex-shrink: 0; background: #000; }
.ep-thumb img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; }
.ep-progress { position: absolute; bottom: 0; left: 0; height: 3px; background: var(--accent); width: 0%; z-index: 2; }
.ep-row.watched .ep-thumb img { opacity: 0.5; }
.ep-row.watched .ep-thumb::after { content: '✓'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #4ade80; font-size: 1.5rem; text-shadow: 0 2px 5px rgba(0,0,0,0.8); z-index: 3; }
.ep-info { display: flex; flex-direction: column; justify-content: center; overflow: hidden; }
.ep-top { display: flex; justify-content: space-between; margin-bottom: 4px; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
.ep-title { color: white; font-weight: 600; font-size: 0.9rem; line-height: 1.3; }

@media (max-width: 1024px) { .watch-layout { flex-direction: column; } .side-col { width: 100%; } .tv-panel { position: static; max-height: 600px; } .poster-3d-wrapper { display: none; } }
</style>

<script>
    const RUNTIME = <?= $runtime ?>;
    if(RUNTIME > 0) {
        const now = new Date(); now.setMinutes(now.getMinutes() + RUNTIME);
        document.getElementById('finishTime').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
    function switchServer(url, btn) {
        document.getElementById('mainPlayer').src = url;
        document.querySelectorAll('.server-tab').forEach(t => t.classList.remove('active')); btn.classList.add('active');
    }
    const playerMount = document.getElementById('playerSection');
    let initialTop = playerMount.getBoundingClientRect().top + window.scrollY;
    window.addEventListener('scroll', () => {
        if(window.scrollY > initialTop + playerMount.offsetHeight && window.innerWidth > 1024) playerMount.classList.add('pip-mode'); else playerMount.classList.remove('pip-mode');
    });

    const key = `series_<?= $id ?>`;
    let prog = JSON.parse(localStorage.getItem(key)) || { watched: [] };
    setTimeout(() => {
        const code = `prog_<?= $season ?>_<?= $episode ?>`;
        if(!prog.watched.includes(code)) prog.watched.push(code);
        localStorage.setItem(key, JSON.stringify(prog));
        document.getElementById(code).style.width = '100%';
        document.querySelector('.ep-row.active').classList.add('watched');
    }, 10000);
    document.querySelectorAll('.ep-row').forEach(row => {
        if(prog.watched.includes(row.dataset.code)) {
            row.classList.add('watched');
            document.getElementById(row.dataset.code).style.width = '100%';
        }
    });

    const activeEp = document.querySelector('.ep-row.active');
    if(activeEp) {
        const epList = document.getElementById('epList');
        if(epList) epList.scrollTop = activeEp.offsetTop - epList.offsetTop - 50;
    }

    // 6. Interactive Ratings
    const stars = document.querySelectorAll('.star');
    stars.forEach(s => {
        s.addEventListener('mouseover', function() {
            let val = parseInt(this.dataset.val);
            stars.forEach(st => { st.style.color = (parseInt(st.dataset.val) <= val) ? 'var(--primary)' : 'rgba(255,255,255,0.2)'; });
        });
        s.addEventListener('mouseout', function() {
            stars.forEach(st => st.style.color = st.classList.contains('selected') ? 'var(--primary)' : 'rgba(255,255,255,0.2)');
        });
        s.addEventListener('click', async function() {
            let val = parseInt(this.dataset.val);
            stars.forEach(st => {
                st.classList.remove('selected');
                if(parseInt(st.dataset.val) <= val) st.classList.add('selected');
                st.style.color = st.classList.contains('selected') ? 'var(--primary)' : 'rgba(255,255,255,0.2)';
            });
            const res = await fetch('/api/rate', { method: 'POST', body: JSON.stringify({ id: TMDB_ID, type: TYPE, rating: val }) });
            const data = await res.json();
            if(data.error) window.location.href = '/login';
            else alert('Thank you for rating!');
        });
    });

    // 7. Stream Report
    async function reportStream() {
        await fetch('/api/report', { method: 'POST', body: JSON.stringify({ id: TMDB_ID, type: TYPE }) });
        alert("Server has been flagged for review. A technician will replace the source within 24 hours.");
    }
</script>
