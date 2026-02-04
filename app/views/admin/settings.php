<div class="card">
    <div class="tabs">
        <div class="tab active" data-target="general">General</div>
        <div class="tab" data-target="seo">SEO & Analytics</div>
        <div class="tab" data-target="mail">Mail (SMTP)</div>
        <div class="tab" data-target="auth">Google Auth</div>
    </div>

    <form action="/admin/update" method="POST" enctype="multipart/form-data">
        
        <!-- General -->
        <div id="general" class="tab-content active">
            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
            
            <label>Favicon / Site Icon</label>
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 15px;">
                <?php if(!empty($settings['site_favicon'])): ?>
                    <img src="<?= htmlspecialchars($settings['site_favicon']) ?>" style="width: 32px; height: 32px; border-radius: 4px; border: 1px solid var(--border);">
                <?php endif; ?>
                <input type="file" name="site_favicon" accept="image/png, image/jpeg, image/x-icon, image/svg+xml">
            </div>
        </div>

        <!-- SEO -->
        <div id="seo" class="tab-content">
            <label>SEO Description</label>
            <textarea name="seo_description"><?= htmlspecialchars($settings['seo_description'] ?? '') ?></textarea>
            <label>Header Code (CSS/Verifications)</label>
            <textarea name="site_header_code" style="font-family: monospace; height: 150px;"><?= htmlspecialchars($settings['site_header_code'] ?? '') ?></textarea>
            <label>Footer Code (JS/Analytics)</label>
            <textarea name="site_footer_code" style="font-family: monospace; height: 150px;"><?= htmlspecialchars($settings['site_footer_code'] ?? '') ?></textarea>
        </div>

        <!-- Mail -->
        <div id="mail" class="tab-content">
            <label>SMTP Host</label>
            <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>">
            <label>SMTP Port</label>
            <input type="text" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>">
            <label>SMTP User (Email)</label>
            <input type="text" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>">
            <label>SMTP Password</label>
            <input type="password" name="smtp_pass" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>">
             <div style="margin-top: 10px;">
                <a href="/admin/test-smtp" class="btn" style="background: transparent; border: 1px solid var(--border); font-size: 0.8rem;">Test SMTP Connection</a>
            </div>
        </div>

        <!-- Auth -->
        <div id="auth" class="tab-content">
            <label>Google Client ID</label>
            <input type="text" name="google_client_id" value="<?= htmlspecialchars($settings['google_client_id'] ?? '') ?>">
            <label>Google Client Secret</label>
            <input type="password" name="google_client_secret" value="<?= htmlspecialchars($settings['google_client_secret'] ?? '') ?>">
            <label>Redirect URI</label>
            <?php 
                // Always use dynamic URL based on SITE_URL or current host
                if (defined('SITE_URL') && strpos(SITE_URL, 'localhost') === false) {
                    $defaultUri = SITE_URL . '/auth/google/callback';
                } else {
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $domain = $_SERVER['HTTP_HOST'];
                    $defaultUri = $protocol . $domain . '/auth/google/callback';
                }
            ?>
            <input type="text" name="google_redirect_uri" value="<?= htmlspecialchars($defaultUri) ?>" readonly>
            <p style="font-size: 0.8rem; color: var(--text-muted);">Copy this Redirect URI to your Google Cloud Console.</p>
        </div>

        <div style="margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
            <button type="submit" class="btn">Save Changes</button>
        </div>
    </form>
</div>
