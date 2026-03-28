<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
    <div class="section-header" style="margin-top: 20px; flex-direction: column; align-items: flex-start; gap: 20px;">
        <h2 class="section-title"><span><?= htmlspecialchars($pageTitle) ?></span></h2>
        
        <div class="filter-pills">
            <button class="filter-pill active" onclick="setFilter('', this)">All</button>
            <?php if($category == 'movie'): ?>
                <button class="filter-pill" onclick="setFilter('28', this)">Action</button>
                <button class="filter-pill" onclick="setFilter('12', this)">Adventure</button>
                <button class="filter-pill" onclick="setFilter('35', this)">Comedy</button>
                <button class="filter-pill" onclick="setFilter('27', this)">Horror</button>
                <button class="filter-pill" onclick="setFilter('878', this)">Sci-Fi</button>
            <?php elseif($category == 'tv'): ?>
                <button class="filter-pill" onclick="setFilter('10759', this)">Action & Adv</button>
                <button class="filter-pill" onclick="setFilter('16', this)">Animation</button>
                <button class="filter-pill" onclick="setFilter('35', this)">Comedy</button>
                <button class="filter-pill" onclick="setFilter('18', this)">Drama</button>
                <button class="filter-pill" onclick="setFilter('10765', this)">Sci-Fi & Fantasy</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid" id="infinite-grid">
        <!-- Content loaded via JS -->
    </div>
    
    <div id="loading" style="text-align: center; padding: 40px; display: none;">
        <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    .filter-pills { display: flex; gap: 10px; overflow-x: auto; max-width: 100%; padding-bottom: 5px; scrollbar-width: none; }
    .filter-pills::-webkit-scrollbar { display: none; }
    .filter-pill { background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border); padding: 8px 20px; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; font-size: 0.9rem; }
    .filter-pill:hover { background: rgba(255,255,255,0.1); color: var(--text); }
    .filter-pill.active { background: var(--primary); color: #000; border-color: var(--primary); }
</style>

<script>
    let page = 1;
    let category = '<?= $category ?>';
    let currentGenre = '';
    let isLoading = false;
    const grid = document.getElementById('infinite-grid');
    const loading = document.getElementById('loading');

    // Initial Load
    loadMore();

    function setFilter(genre, btn) {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        currentGenre = genre;
        page = 1;
        grid.innerHTML = '';
        window.addEventListener('scroll', scrollHandler); // Re-attach scroll if removed
        loadMore();
    }

    function scrollHandler() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
            loadMore();
        }
    }
    
    window.addEventListener('scroll', scrollHandler);

    async function loadMore() {
        if (isLoading) return;
        isLoading = true;
        loading.style.display = 'block';
        
        try {
            const res = await fetch(`/api/browse?type=${category}&page=${page}&genre=${currentGenre}`);
            const data = await res.json();
            
            if (data.results && data.results.length > 0) {
                data.results.forEach(m => {
                    if (!m.poster_path) return;
                    const type = (category === 'movie') ? 'movie' : 'tv';
                    const title = m.title || m.name;
                    const date = (m.release_date || m.first_air_date || '').substring(0, 4);
                    
                    const div = document.createElement('a');
                    div.href = `/watch/${m.id}?type=${type}`;
                    div.className = 'movie-card animate-fade-in';
                    div.innerHTML = `
                        <img src="https://image.tmdb.org/t/p/w342${m.poster_path}" class="poster" loading="lazy" alt="${title}">
                        <span class="title">${title}</span>
                        <div class="meta">${date}</div>
                    `;
                    grid.appendChild(div);
                });
                page++;
            } else {
                // End of content
                window.removeEventListener('scroll', loadMore);
            }
        } catch(e) {
            console.error(e);
        }
        
        isLoading = false;
        loading.style.display = 'none';
    }
</script>
