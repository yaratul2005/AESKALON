<div class="container animate-fade-in" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    
    <!-- Premium Profile Header -->
    <div class="glass-panel dashboard-header-premium">
        <div class="cover-image"></div>
        
        <div class="profile-content">
            <img src="<?= htmlspecialchars($user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username']) ?>" class="profile-avatar-xl">
            
            <div class="profile-info" style="flex: 1;">
                <h1><?= htmlspecialchars($user['username']) ?></h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 5px;"><?= htmlspecialchars($user['email']) ?></p>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <div class="badge" style="background: rgba(56, 189, 248, 0.2); color: var(--primary);">Free Plan</div>
                    <?php if($user['is_admin'] ?? false): ?>
                        <div class="badge" style="background: rgba(244, 114, 182, 0.2); color: var(--accent);">Admin</div>
                    <?php endif; ?>
                </div>
            </div>

            <button onclick="document.getElementById('editProfileModal').style.display='flex'" class="btn-view-all" style="background: var(--surface); border: 1px solid var(--border); color: var(--text); padding: 12px 24px; cursor: pointer;">
                Edit Profile
            </button>
        </div>
    </div>

    <!-- Premium Tabs -->
    <div class="tabs-premium">
        <div class="tab-pill active" onclick="switchTab(this, 'history')">
            History
        </div>
        <div class="tab-pill" onclick="switchTab(this, 'later')">
            Watch Later
        </div>
        <div class="tab-pill" onclick="alert('Coming Soon!')">
            Settings
        </div>
    </div>

    <!-- Content Area -->
    <div style="min-height: 400px;">
        <!-- Watch History -->
        <div id="history" class="tab-content active">
            <?php if(empty($history)): ?>
                <div style="text-align: center; padding: 80px 20px; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;">⏳</div>
                    <h3>No history yet</h3>
                    <p>Movies and shows you watch will appear here.</p>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach($history as $item): ?>
                        <div class="media-card-premium load-tmdb" data-id="<?= $item['tmdb_id'] ?>" data-type="<?= $item['type'] ?>">
                            <div class="poster-placeholder" style="width: 100%; height: 100%; background: #334155;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Watch Later -->
        <div id="later" class="tab-content" style="display: none;">
            <?php if(empty($watchLater)): ?>
                <div style="text-align: center; padding: 80px 20px; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;">🔖</div>
                    <h3>Your list is empty</h3>
                    <p>Save movies to watch later.</p>
                </div>
            <?php else: ?>
                 <div class="grid">
                    <?php foreach($watchLater as $item): ?>
                        <div class="media-card-premium load-tmdb" data-id="<?= $item['tmdb_id'] ?>" data-type="<?= $item['type'] ?>">
                             <div class="poster-placeholder" style="width: 100%; height: 100%; background: #334155;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Premium Modal -->
<div id="editProfileModal" class="modal-glass" style="display: none; position: fixed; inset: 0; z-index: 3000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
    <div class="glass-panel" style="padding: 40px; width: 100%; max-width: 450px; position: relative; transform: scale(0.95); transition: transform 0.3s;">
        <h2 style="margin-top: 0; font-size: 1.8rem;">Edit Profile</h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Update your personal details.</p>
        
        <form action="/dashboard/update" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #cbd5e1;">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-input-premium">
            </div>
            
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #cbd5e1;">New Password</label>
                <input type="password" name="password" placeholder="Leave empty to keep current" class="form-input-premium">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">Min 6 characters.</p>
            </div>

            <button type="submit" class="btn-play" style="width: 100%; border: none; cursor: pointer; justify-content: center;">Save Changes</button>
        </form>
        
        <button onclick="closeModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.5rem; transition: color 0.2s;">&times;</button>
    </div>
</div>

<script>
// Tab Switching
function switchTab(el, target) {
    document.querySelectorAll('.tab-pill').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => {
        c.style.display = 'none';
        c.classList.remove('active');
    });
    
    el.classList.add('active');
    const content = document.getElementById(target);
    content.style.display = 'block';
    
    // Add fade animation
    content.style.animation = 'none';
    content.offsetHeight; /* trigger reflow */
    content.style.animation = 'fadeIn 0.4s ease forwards';
}

// Modal Logic
const modal = document.getElementById('editProfileModal');
const modalPanel = modal.querySelector('.glass-panel');

function openModal() { // Called by button onClick
    modal.style.display = 'flex';
    // Small timeout for CSS transition
    setTimeout(() => {
        modal.style.opacity = '1';
        modalPanel.style.transform = 'scale(1)';
    }, 10);
}
// Override inline onclick with function for cleaner anims
document.querySelector('[onclick*="editProfileModal"]').onclick = openModal;

function closeModal() {
    modal.style.opacity = '0';
    modalPanel.style.transform = 'scale(0.95)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Fetch TMDB Logic (matches previous version but using new classes)
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.load-tmdb');
    const API_KEY = '<?= TMDB_API_KEY ?>';
    
    cards.forEach(async card => {
        const id = card.dataset.id;
        const type = card.dataset.type;
        try {
            const res = await fetch(`https://api.themoviedb.org/3/${type}/${id}?api_key=${API_KEY}`);
            const data = await res.json();
             // High res poster
            const poster = data.poster_path ? `https://image.tmdb.org/t/p/w500${data.poster_path}` : 'https://placehold.co/400x600?text=No+Image';
            
            card.innerHTML = `
                <a href="/watch/${id}?type=${type}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                    <img src="${poster}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); transform: translateY(100%); transition: transform 0.3s;">
                        <span style="font-weight: 700; font-size: 0.9rem; text-shadow: 0 2px 4px black;">${data.title || data.name}</span>
                    </div>
                </a>
            `;
            
            // Hover effect for title
            card.addEventListener('mouseenter', () => {
                const title = card.querySelector('div[style*="position: absolute"]');
                if(title) title.style.transform = 'translateY(0)';
            });
            card.addEventListener('mouseleave', () => {
                const title = card.querySelector('div[style*="position: absolute"]');
                if(title) title.style.transform = 'translateY(100%)';
            });
            
        } catch(e) { console.error(e); }
    });
});
</script>
