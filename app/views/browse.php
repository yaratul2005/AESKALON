<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
    <div class="section-header" style="margin-top: 20px;">
        <h2 class="section-title"><span><?= htmlspecialchars($pageTitle) ?></span></h2>
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
</style>

<script>
    let page = 1;
    let category = '<?= $category ?>';
    let isLoading = false;
    const grid = document.getElementById('infinite-grid');
    const loading = document.getElementById('loading');

    // Initial Load
    loadMore();

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
            loadMore();
        }
    });

    async function loadMore() {
        if (isLoading) return;
        isLoading = true;
        loading.style.display = 'block';
        
        try {
            const res = await fetch(`/api/browse?type=${category}&page=${page}`);
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
