<div class="card">
    <h3>Ban API/User</h3>
    <form action="/admin/ban-ip" method="POST" style="display: flex; gap: 10px;">
        <input type="text" name="ip" placeholder="IP Address to Ban" required style="margin-bottom: 0;">
        <input type="text" name="reason" placeholder="Reason (Optional)" style="margin-bottom: 0;">
        <button type="submit" class="btn" style="background: #f87171;">Ban IP</button>
    </form>
</div>

<div class="card">
    <h3>Active Bans</h3>
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="color: var(--text-muted); border-bottom: 1px solid var(--border);">
                <th style="padding: 10px;">IP Address</th>
                <th style="padding: 10px;">Reason</th>
                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bans as $ban): ?>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 10px;"><?= htmlspecialchars($ban['ip_address']) ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($ban['reason']) ?></td>
                <td style="padding: 10px;"><?= $ban['banned_at'] ?></td>
                <td style="padding: 10px;">
                    <form action="/admin/unban-ip" method="POST">
                        <input type="hidden" name="ip" value="<?= $ban['ip_address'] ?>">
                        <button type="submit" class="btn" style="background: transparent; color: #f87171; padding: 5px;">Unban</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
