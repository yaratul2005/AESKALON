

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
    <!-- Comments Section -->
    <div class="comments-container">
        <h3 style="margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            Comments <span id="commentCount" class="badge" style="margin: 0; background: var(--surface); color: var(--text);">0</span>
        </h3>

        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="comment-input-wrapper">
                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.$_SESSION['user_username']) ?>" class="comment-avatar">
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <textarea id="commentBox" class="comment-box" placeholder="Join the discussion..."></textarea>
                    <button onclick="postComment()" class="btn-send">Post Comment</button>
                </div>
            </div>
        <?php else: ?>
            <div style="background: rgba(56, 189, 248, 0.1); padding: 20px; border-radius: 12px; text-align: center; color: var(--primary); margin-bottom: 30px;">
                <p style="margin: 0;">Please <a href="/login" style="font-weight: 700; text-decoration: underline;">Login</a> to leave a comment.</p>
            </div>
        <?php endif; ?>

        <div id="commentsList">
            <!-- Populated via JS -->
            <div class="loader" style="text-align: center; color: var(--text-muted); padding: 20px;">Loading comments...</div>
        </div>
    </div>
</div>

<script>
    const TMDB_ID = <?= json_encode($id) ?>;
    const TYPE = <?= json_encode($type) ?>;
    const USER_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

    document.addEventListener('DOMContentLoaded', loadComments);

    async function loadComments() {
        try {
            const res = await fetch(`/api/comments?id=${TMDB_ID}&type=${TYPE}`);
            const data = await res.json();
            const list = document.getElementById('commentsList');
            document.getElementById('commentCount').innerText = data.threads.length;
            list.innerHTML = '';

            if(data.threads.length === 0) {
                list.innerHTML = '<p style="text-align:center; color: var(--text-muted); padding: 20px;">Be the first to comment!</p>';
                return;
            }

            data.threads.forEach(t => {
                const replies = data.replies[t.id] || [];
                const html = `
                    <div class="comment-thread">
                        <img src="${t.avatar || 'https://ui-avatars.com/api/?name='+t.username+'&background=random'}" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">${t.username}</span>
                                <span class="comment-date">${new Date(t.created_at).toLocaleDateString()}</span>
                            </div>
                            <div class="comment-body">${t.body}</div>
                            
                            <div class="comment-actions">
                                <button onclick="toggleReplyBox(${t.id})" class="action-link">Reply</button>
                            </div>

                            <div id="reply-box-${t.id}" style="display: none; margin-top: 15px; animation: fadeIn 0.3s ease;">
                                ${USER_LOGGED_IN ? `
                                    <div style="display: flex; gap: 10px;">
                                        <input type="text" id="reply-input-${t.id}" class="form-input-premium" placeholder="Write a reply..." style="padding: 10px;">
                                        <button onclick="postComment(${t.id})" class="btn-send" style="margin: 0; padding: 0 20px;">Send</button>
                                    </div>
                                ` : ''}
                            </div>

                            ${replies.length > 0 ? `
                                <div class="replies-list">
                                    ${replies.map(r => `
                                        <div class="comment-thread reply-item">
                                            <img src="${r.avatar || 'https://ui-avatars.com/api/?name='+r.username}" class="comment-avatar" style="width: 35px; height: 35px;">
                                            <div class="comment-content">
                                                <div class="comment-header">
                                                    <span class="comment-author">${r.username}</span>
                                                    <span class="comment-date">${new Date(r.created_at).toLocaleDateString()}</span>
                                                </div>
                                                <div class="comment-body" style="background: rgba(255,255,255,0.02);">${r.body}</div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                list.innerHTML += html;
            });
        } catch(e) { console.error(e); }
    }

    function toggleReplyBox(id) {
        if(!USER_LOGGED_IN) {
            window.location.href = '/login';
            return;
        }
        const el = document.getElementById(`reply-box-${id}`);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
        if(el.style.display === 'block') {
             setTimeout(() => document.getElementById(`reply-input-${id}`).focus(), 100);
        }
    }

    async function postComment(parentId = null) {
        const inputId = parentId ? `reply-input-${parentId}` : 'commentBox';
        const input = document.getElementById(inputId);
        const body = input.value;
        if (!body.trim()) return;

        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = 'Posting...';
        btn.disabled = true;

        try {
            const res = await fetch('/api/comments', {
                method: 'POST',
                body: JSON.stringify({ id: TMDB_ID, type: TYPE, body, parentId })
            });
            const data = await res.json();
            
            if (data.status === 'ok') {
                input.value = '';
                if(parentId) toggleReplyBox(parentId);
                loadComments(); // Refresh list
            } else {
                alert(data.error || 'Failed to post');
            }
        } catch (e) {
            alert('Error connecting to server');
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }
</script>
        
        if (data.error) alert(data.error);
        else {
            if (parentId) document.getElementById(`reply-input-${parentId}`).value = '';
            else document.getElementById('commentBox').value = '';
            loadComments();
        }
    }

    loadComments();
</script>
