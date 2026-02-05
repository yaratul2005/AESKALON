<?php require_once '../app/views/layout.php'; require_once '../core/Csrf.php'; ?>

<main style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
        <h1>Account Settings</h1>
        <a href="/dashboard" class="btn" style="background: var(--surface); color: var(--text);">Back to Dashboard</a>
    </div>

    <!-- Feedback -->
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: rgba(248, 113, 113, 0.2); color: #f87171; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: rgba(52, 211, 153, 0.2); color: #34d399; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="/update-profile" method="POST" enctype="multipart/form-data">
        <?= Csrf::input() ?>
        
        <!-- Profile Section -->
        <div class="glass-panel" style="padding: 30px; margin-bottom: 30px;">
            <h2 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">Public Profile</h2>
            
            <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
                <!-- Avatar -->
                <div style="flex: 0 0 150px; text-align: center;">
                    <img src="<?= $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random' ?>" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--surface); margin-bottom: 15px;">
                    <label class="btn" style="cursor: pointer; display: block; font-size: 0.9rem;">
                        Change Photo
                        <input type="file" name="avatar" style="display: none;" accept="image/*">
                    </label>
                </div>

                <!-- Fields -->
                <div style="flex: 1; min-width: 250px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; color: var(--text-muted); margin-bottom: 5px;">Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required 
                               style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(0,0,0,0.2); color: white;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; color: var(--text-muted); margin-bottom: 5px;">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required 
                               style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(0,0,0,0.2); color: white;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; color: var(--text-muted); margin-bottom: 5px;">Bio</label>
                        <textarea name="bio" rows="3" placeholder="Tell us about yourself..." 
                                  style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(0,0,0,0.2); color: white; resize: vertical;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Section -->
        <div class="glass-panel" style="padding: 30px; margin-bottom: 30px;">
            <h2 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">Security</h2>
            
            <div style="margin-top: 20px;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Leave blank if you don't want to change your password.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; color: var(--text-muted); margin-bottom: 5px;">Current Password</label>
                        <input type="password" name="current_password" placeholder="••••••••" 
                               style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(0,0,0,0.2); color: white;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); margin-bottom: 5px;">New Password</label>
                        <input type="password" name="new_password" placeholder="••••••••" 
                               style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(0,0,0,0.2); color: white;">
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: right; margin-bottom: 50px;">
            <button type="submit" class="btn" style="background: var(--primary); padding: 12px 30px; font-size: 1rem;">Save Changes</button>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="glass-panel" style="padding: 30px; border: 1px solid #7f1d1d; background: rgba(69, 10, 10, 0.4);">
        <h2 style="margin-top: 0; color: #f87171; border-bottom: 1px solid rgba(248, 113, 113, 0.2); padding-bottom: 15px;">Danger Zone</h2>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <div>
                <h4 style="margin: 0; color: white;">Delete Account</h4>
                <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem;">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
            <form action="/delete-account" method="POST" onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');">
                <?= Csrf::input() ?>
                <input type="hidden" name="confirm_delete" value="1">
                <button type="submit" class="btn" style="background: #ef4444; color: white;">Delete Account</button>
            </form>
        </div>
    </div>

</main>
