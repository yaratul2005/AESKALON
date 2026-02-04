

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
        <div style="display: flex; gap: 20px; flex: 1;">
            <img src="https://image.tmdb.org/t/p/w200<?= $movie['poster_path'] ?>" class="poster-thumb">
            <div class="meta-info">
                <h1><?= htmlspecialchars($title) ?></h1>
                <p class="overview"><?= htmlspecialchars($movie['overview']) ?></p>
            </div>
        </div>
        <div>
           <button id="btnWatchLater" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid var(--border); padding: 10px 20px;">
                + Watch Later
            </button>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="container" style="margin-top: 50px; padding-top: 30px; border-top: 1px solid var(--border);">
        <h3>Comments</h3>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div style="margin-bottom: 30px; display: flex; gap: 15px;">
                <img src="<?= $_SESSION['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.$_SESSION['user_username'].'&background=random' ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                <div style="flex: 1;">
                    <textarea id="commentBox" placeholder="Write a comment..." style="width: 100%; background: var(--surface); border: 1px solid var(--border); padding: 10px; color: white; border-radius: 8px; min-height: 80px;"></textarea>
                    <div style="text-align: right; margin-top: 10px;">
                        <button onclick="postComment()" class="btn" style="font-size: 0.9rem;">Post Comment</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p style="margin-bottom: 30px;"><a href="/login" style="color: var(--primary);">Login</a> to post comments.</p>
        <?php endif; ?>

        <div id="commentsList"></div>
    </div>
</div>

<script>
    const TMDB_ID = '<?= $id ?>';
    const TYPE = '<?= $type ?>';
    
    // Watch Later Logic
    document.getElementById('btnWatchLater').addEventListener('click', async () => {
             const res = await fetch('/api/watch-later', {
                 method: 'POST',
                 body: JSON.stringify({ id: TMDB_ID, type: TYPE })
             });
             const data = await res.json();
             if (data.error) window.location.href = '/login';
             else if (data.status === 'added') alert('Added to Watch Later');
             else alert('Removed from Watch Later');
    });

    // Add to History automatically
    setTimeout(() => {
             fetch('/api/history', { method: 'POST', body: JSON.stringify({ id: TMDB_ID, type: TYPE }) });
    }, 10000); 

    // Comments System
    async function loadComments() {
        const res = await fetch(`/api/comments?id=${TMDB_ID}&type=${TYPE}`);
        const data = await res.json();
        const list = document.getElementById('commentsList');
        list.innerHTML = '';

        data.threads.forEach(t => {
            const replies = data.replies[t.id] || [];
            const html = `
                <div class="comment" style="margin-bottom: 20px;">
                    <div style="display: flex; gap: 15px;">
                        <img src="${t.avatar || 'https://ui-avatars.com/api/?name='+t.username+'&background=random'}" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.9rem;">${t.username} <span style="font-weight: 400; color: var(--text-muted); font-size: 0.8rem;">• ${new Date(t.created_at).toLocaleDateString()}</span></div>
                            <div style="margin-top: 5px; color: #cbd5e1;">${t.body}</div>
                            <button onclick="toggleReplyBox(${t.id})" style="background: none; border: none; color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; cursor: pointer;">Reply</button>
                            
                            <div id="reply-box-${t.id}" style="display: none; margin-top: 10px;">
                                <input type="text" id="reply-input-${t.id}" placeholder="Reply..." style="width:100%; background: var(--bg); border: 1px solid var(--border); padding: 8px; color: white; border-radius: 4px; margin-bottom: 5px;">
                                <button onclick="postComment(${t.id})" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Send</button>
                            </div>
                        </div>
                    </div>
                    <!-- Replies -->
                    <div style="margin-left: 50px; margin-top: 10px; border-left: 2px solid var(--border); padding-left: 15px;">
                        ${replies.map(r => `
                            <div style="margin-top: 10px;">
                                <div style="font-weight: 600; font-size: 0.85rem;">${r.username}</div>
                                <div style="font-size: 0.9rem; color: #cbd5e1;">${r.body}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            list.innerHTML += html;
        });
    }

    function toggleReplyBox(id) {
        const el = document.getElementById(`reply-box-${id}`);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    async function postComment(parentId = null) {
        const body = parentId ? document.getElementById(`reply-input-${parentId}`).value : document.getElementById('commentBox').value;
        if (!body) return;

        const res = await fetch('/api/comments', {
            method: 'POST',
            body: JSON.stringify({ id: TMDB_ID, type: TYPE, body, parentId })
        });
        const data = await res.json();
        
        if (data.error) alert(data.error);
        else {
            if (parentId) document.getElementById(`reply-input-${parentId}`).value = '';
            else document.getElementById('commentBox').value = '';
            loadComments();
        }
    }

    loadComments();
</script>
