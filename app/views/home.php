<?php
    // Limit sliders to show only first 12 items on homepage
    // The rest are accessible via "View All"
    $trending = array_slice($trending, 0, 12);
    $series = array_slice($series, 0, 12);
    $anime = array_slice($anime, 0, 12);
?>

<?php 
    // Take top 5 trending for Hero
    $heroItems = array_slice($trending, 0, 5); 
?>

<?php if(!empty($heroItems)): ?>
<div class="hero-slider" id="heroSlider">
    <?php foreach($heroItems as $index => $item): 
        $bg = "https://image.tmdb.org/t/p/original" . $item['backdrop_path'];
        $title = $item['title'] ?? $item['name'];
        $desc = substr($item['overview'], 0, 200) . '...';
        $id = $item['id'];
        $type = isset($item['title']) ? 'movie' : 'tv';
    ?>
    <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" style="background-image: url('<?= $bg ?>');">
        <div class="hero-info">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <span class="badge" style="margin: 0;">#<?= $index + 1 ?> Trending</span>
                <span class="pulse-dot"></span> <span style="color: var(--primary); font-weight: bold; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Live</span>
            </div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($desc) ?></p>
            <div style="display: flex; gap: 15px;">
                <a href="/watch/<?= $id ?>?type=<?= $type ?>" class="btn-play">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;"><path d="M8 5v14l11-7z"/></svg> Play Now
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <!-- Carousel Controls -->
    <div class="hero-controls">
        <div class="hero-progress-bar"><div class="hero-progress-fill" id="heroProgress"></div></div>
        <div class="hero-thumbnails">
            <?php foreach($heroItems as $index => $item): ?>
                <div class="hero-thumb <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)">
                    <img src="https://image.tmdb.org/t/p/w200<?= $item['poster_path'] ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.hero-slider { position: relative; height: 75vh; min-height: 600px; overflow: hidden; background: #000; }
.hero-slide { position: absolute; inset: 0; background-size: cover; background-position: center top; opacity: 0; transition: opacity 1s ease-in-out, transform 7s linear; transform: scale(1.05); z-index: 1; }
.hero-slide::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, var(--bg) 0%, rgba(8,10,15,0.7) 40%, rgba(8,10,15,0.3) 100%); z-index: 1; }
.hero-slide.active { opacity: 1; transform: scale(1); z-index: 2; }
.hero-slide .hero-info { position: absolute; bottom: 120px; left: 4%; right: 4%; z-index: 3; max-width: 800px; transform: translateY(30px); opacity: 0; transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s; pointer-events: none;}
.hero-slide.active .hero-info { transform: translateY(0); opacity: 1; pointer-events: auto; }
.pulse-dot { width: 10px; height: 10px; background: var(--primary); border-radius: 50%; box-shadow: 0 0 10px var(--primary); animation: pulse 1.5s infinite; }
@keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(14, 165, 233, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); } }
.hero-controls { position: absolute; bottom: 30px; left: 4%; right: 4%; z-index: 5; display: flex; flex-direction: column; gap: 15px; }
.hero-progress-bar { width: 100%; height: 3px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden; }
.hero-progress-fill { height: 100%; width: 0%; background: var(--accent); transition: width 0.1s linear; }
.hero-thumbnails { display: flex; gap: 10px; align-items: center; justify-content: flex-end; }
.hero-thumb { width: 100px; aspect-ratio: 16/9; border-radius: 6px; overflow: hidden; cursor: pointer; border: 2px solid transparent; opacity: 0.5; transition: all 0.3s ease; }
.hero-thumb.active, .hero-thumb:hover { opacity: 1; border-color: var(--accent); transform: scale(1.05); }
.hero-thumb img { width: 100%; height: 100%; object-fit: cover; }
@media (max-width: 768px) { .hero-thumbnails { display: none; } .hero-slide .hero-info { bottom: 60px; } .hero-slider { height: 60vh; min-height: 450px;} }
</style>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const thumbs = document.querySelectorAll('.hero-thumb');
    const progress = document.getElementById('heroProgress');
    const slideDuration = 7000;
    let slideTimer;
    let progressInterval;
    let startTime;

    function goToSlide(index) {
        if(!slides[currentSlide]) return;
        slides[currentSlide].classList.remove('active');
        if(thumbs[currentSlide]) thumbs[currentSlide].classList.remove('active');
        
        currentSlide = index;
        
        slides[currentSlide].classList.add('active');
        if(thumbs[currentSlide]) thumbs[currentSlide].classList.add('active');
        
        resetTimer();
    }

    function nextSlide() {
        goToSlide((currentSlide + 1) % slides.length);
    }

    function resetTimer() {
        clearInterval(progressInterval);
        clearTimeout(slideTimer);
        if(!progress) return;
        progress.style.width = '0%';
        startTime = Date.now();
        
        progressInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const percent = (elapsed / slideDuration) * 100;
            progress.style.width = Math.min(percent, 100) + '%';
        }, 30);
        
        slideTimer = setTimeout(nextSlide, slideDuration);
    }

    // Start auto-rotation
    if(slides.length > 1) resetTimer();
</script>
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
            <span class="title"><?= htmlspecialchars($m['title'] ?? $m['name']) ?></span>
            <div class="meta"><?= substr($m['release_date'] ?? $m['first_air_date'] ?? '', 0, 4) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Upcoming Movies -->
<?php if (!empty($upcoming)): ?>
<div class="section-header">
    <h2 class="section-title"><span>Upcoming Releases</span></h2>
</div>
<div class="scroller">
    <?php foreach (array_slice($upcoming, 0, 15) as $u): 
        $date = $u['release_date'] ?? '';
        if (!$date || strtotime($date) < time()) continue; // Skip if already out
    ?>
        <a href="/watch/<?= $u['id'] ?>?type=movie" class="movie-card upcoming-card" data-release="<?= $date ?>">
            <img src="https://image.tmdb.org/t/p/w342<?= $u['poster_path'] ?>" class="poster" loading="lazy">
            <span class="title"><?= htmlspecialchars($u['title']) ?></span>
            <div class="countdown-badge">Calculating...</div>
        </a>
    <?php endforeach; ?>
</div>

<style>
.upcoming-card { position: relative; }
.countdown-badge { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.8); border: 1px solid var(--accent); color: var(--accent); padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; z-index: 10; backdrop-filter: blur(4px); box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
</style>

<script>
    function updateCountdowns() {
        document.querySelectorAll('.upcoming-card').forEach(card => {
            const release = new Date(card.dataset.release).getTime();
            const now = new Date().getTime();
            const dist = release - now;
            const badge = card.querySelector('.countdown-badge');
            
            if (dist < 0) {
                badge.innerText = "Released!";
                badge.style.color = "#4ade80";
                badge.style.borderColor = "#4ade80";
                return;
            }
            
            const days = Math.floor(dist / (1000 * 60 * 60 * 24));
            const hours = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            badge.innerText = `${days}d ${hours}h`;
        });
    }
    updateCountdowns();
    setInterval(updateCountdowns, 60000); // 1 minute
</script>
<?php endif; ?>

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

<!-- Stats Section -->
<div class="stats-container animate-fade-in" style="max-width: 1400px; margin: 40px auto; display: flex; flex-wrap: wrap; gap: 30px; justify-content: space-around; background: rgba(255,255,255,0.03); border: 1px solid var(--border); padding: 40px 20px; border-radius: 20px; text-align: center;">
    <div style="flex: 1; min-width: 200px;">
        <h3 class="stat-number" data-target="95642" style="font-size: 3.5rem; color: var(--primary); margin: 0; font-family: 'Bebas Neue', sans-serif;">0</h3>
        <p style="color: var(--text-muted); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1.5px; margin-top: 5px;">Movies & Series</p>
    </div>
    <div style="flex: 1; min-width: 200px;">
        <h3 class="stat-number" data-target="150" style="font-size: 3.5rem; color: var(--accent); margin: 0; font-family: 'Bebas Neue', sans-serif;">0</h3>
        <p style="color: var(--text-muted); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1.5px; margin-top: 5px;">New Daily Elements</p>
    </div>
    <div style="flex: 1; min-width: 200px;">
        <h3 class="stat-number" data-target="24" style="font-size: 3.5rem; color: #4ade80; margin: 0; font-family: 'Bebas Neue', sans-serif;">0</h3>
        <p style="color: var(--text-muted); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1.5px; margin-top: 5px;">Hour Uptime</p>
    </div>
</div>

<script>
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200; 
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                const updateCount = () => {
                    const count = +counter.innerText;
                    const inc = target / speed;
                    if(count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target + (target > 1000 ? '+' : '');
                    }
                };
                updateCount();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
</script>
