<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    
    <div class="dashboard-header" style="display: flex; gap: 30px; align-items: center; margin-bottom: 40px; background: var(--surface); padding: 30px; border-radius: 16px; border: 1px solid var(--border);">
        <img src="<?= htmlspecialchars($user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username']) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;"><?= htmlspecialchars($user['username']) ?></h1>
            <p style="color: var(--text-muted); margin-top: 5px;"><?= htmlspecialchars($user['email']) ?></p>
            <div style="margin-top: 15px;">
                <button onclick="document.getElementById('editProfileModal').style.display='block'" class="btn" style="padding: 8px 16px; font-size: 0.9rem;">Edit Profile</button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs" style="margin-bottom: 20px;">
        <div class="tab active" onclick="switchTab(this, 'history')">Watch History</div>
        <div class="tab" onclick="switchTab(this, 'later')">Watch Later</div>
    </div>

    <!-- Watch History -->
    <div id="history" class="tab-content active">
        <?php if(empty($history)): ?>
            <p style="color: var(--text-muted); text-align: center; padding: 40px;">You haven't watched anything yet.</p>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach($history as $item): ?>
                    <div class="media-card load-tmdb" data-id="<?= $item['tmdb_id'] ?>" data-type="<?= $item['type'] ?>">
                        <!-- JS will populate img/title -->
                        <div class="poster-placeholder" style="aspect-ratio: 2/3; background: var(--surface); border-radius: 12px;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Watch Later -->
    <div id="later" class="tab-content">
        <?php if(empty($watchLater)): ?>
            <p style="color: var(--text-muted); text-align: center; padding: 40px;">Your list is empty.</p>
        <?php else: ?>
             <div class="media-grid">
                <?php foreach($watchLater as $item): ?>
                    <div class="media-card load-tmdb" data-id="<?= $item['tmdb_id'] ?>" data-type="<?= $item['type'] ?>">
                         <div class="poster-placeholder" style="aspect-ratio: 2/3; background: var(--surface); border-radius: 12px;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--bg); padding: 30px; border-radius: 16px; width: 100%; max-width: 400px; position: relative;">
        <h2 style="margin-top: 0;">Edit Profile</h2>
        <form action="/dashboard/update" method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-input" style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: white;">
            
            <label>New Password (Optional)</label>
            <input type="password" name="password" placeholder="Leave empty to keep current" class="form-input" style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: white;">

            <button type="submit" class="btn" style="width: 100%;">Save Changes</button>
        </form>
        <button onclick="document.getElementById('editProfileModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; cursor: pointer;">✕</button>
    </div>
</div>

<script>
function switchTab(el, target) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => {
        c.style.display = 'none';
        c.classList.remove('active');
    });
    el.classList.add('active');
    document.getElementById(target).style.display = 'block';
}
// Initial state
document.getElementById('later').style.display = 'none';

// Fetch TMDB Data for Cards
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.load-tmdb');
    const API_KEY = '<?= TMDB_API_KEY ?>'; // Exposed but restricted by domain usually. Better to use proxy but this is direct implementation as per constraints.
    
    cards.forEach(async card => {
        const id = card.dataset.id;
        const type = card.dataset.type;
        try {
            const res = await fetch(`https://api.themoviedb.org/3/${type}/${id}?api_key=${API_KEY}`);
            const data = await res.json();
            
            card.innerHTML = `
                <a href="/watch/${id}?type=${type}" style="text-decoration: none; color: inherit;">
                    <div class="poster-container" style="position: relative; overflow: hidden; border-radius: 12px; aspect-ratio: 2/3;">
                        <img src="https://image.tmdb.org/t/p/w342${data.poster_path}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="play-overlay"><div class="play-btn">▶</div></div>
                    </div>
                    <h3 class="media-title" style="margin: 10px 0 5px; font-size: 0.9rem;">${data.title || data.name}</h3>
                </a>
            `;
        } catch(e) { console.error(e); }
    });
});
</script>
<style>
.tab-content { animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
