<div class="card">
    <h3>System Status</h3>
    <?php if ($pendingUpdatesCount > 0): ?>
        <p style="color: #facc15;">Details: <?= $pendingUpdatesCount ?> new database updates found.</p>
        <form action="/admin/run-updates" method="POST">
            <button type="submit" class="btn">Run Auto-Updater</button>
        </form>
    <?php else: ?>
        <p style="color: #4ade80;">✔ System is up to date.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Quick Stats</h3>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">v<?= end($appliedVersions) ?></div>
            <div style="color: var(--text-muted);">DB Version</div>
        </div>
        <!-- Add more stats here later -->
    </div>
</div>
