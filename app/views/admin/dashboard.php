<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Total Users -->
    <div class="card" style="text-align: center; padding: 30px;">
        <div style="font-size: 3rem; font-weight: 700; color: #3b82f6;"><?= $totalUsers ?></div>
        <div style="color: var(--text-muted);">Total Users</div>
    </div>

    <!-- New Users -->
    <div class="card" style="text-align: center; padding: 30px;">
        <div style="font-size: 3rem; font-weight: 700; color: #10b981;"><?= $newUsers ?></div>
        <div style="color: var(--text-muted);">New This Week</div>
    </div>

    <!-- Verified -->
    <div class="card" style="text-align: center; padding: 30px;">
        <div style="font-size: 3rem; font-weight: 700; color: #f59e0b;"><?= $verifiedUsers ?></div>
        <div style="color: var(--text-muted);">Verified Accounts</div>
    </div>
    
    <!-- Pending Updates -->
    <div class="card" style="text-align: center; padding: 30px;">
        <div style="font-size: 3rem; font-weight: 700; color: <?= $pendingUpdatesCount > 0 ? '#ef4444' : '#64748b' ?>;"><?= $pendingUpdatesCount ?></div>
        <div style="color: var(--text-muted);">Pending Updates</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Chart -->
    <div class="card">
        <h3>Most Watched Content</h3>
        <canvas id="topContentChart"></canvas>
    </div>

    <!-- System Info -->
    <div class="card">
        <h3>System Status</h3>
        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
        <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?></p>
        <p><strong>DB Connection:</strong> <span style="color: #10b981;">Active</span></p>
        
        <?php if($pendingUpdatesCount > 0): ?>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
        <div style="background: rgba(239, 68, 68, 0.1); padding: 15px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3);">
            <h4 style="margin: 0 0 10px; color: #ef4444;">Updates Available</h4>
            <p style="font-size: 0.9rem; margin-bottom: 10px;">Create a backup before updating.</p>
            <form action="/admin/run-updates" method="POST">
                <button type="submit" class="btn" style="background: #ef4444; width: 100%;">Apply <?= $pendingUpdatesCount ?> Updates</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const ctx = document.getElementById('topContentChart').getContext('2d');
    
    // PHP to JS Data Transfer
    const labels = <?= json_encode(array_column($topContent, 'tmdb_id')) ?>;
    const data = <?= json_encode(array_column($topContent, 'views')) ?>;
    const types = <?= json_encode(array_column($topContent, 'type')) ?>;
    
    // Prettify Labels (add Type prefix)
    const prettyLabels = labels.map((id, index) => `${types[index].toUpperCase()} #${id}`);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: prettyLabels,
            datasets: [{
                label: 'Views',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
