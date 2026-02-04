<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password - Great10</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: var(--surface); padding: 40px; border-radius: 16px; width: 100%; max-width: 400px; border: 1px solid var(--border); text-align: center; }
        .auth-input { width: 100%; padding: 12px; margin-bottom: 15px; background: var(--bg); border: 1px solid var(--border); color: var(--text); border-radius: 8px; }
        .auth-btn { width: 100%; padding: 12px; background: var(--primary); color: #000; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2 style="margin-top: 0;">Complete Setup</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Email verified! Now set a password to finish.</p>

        <form action="/complete-registration" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="password" class="auth-input" placeholder="New Password" required minlength="6">
            <button type="submit" class="auth-btn">Complete Registration</button>
        </form>
    </div>
</div>

</body>
</html>
