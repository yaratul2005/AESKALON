<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Great10</title>
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
        <h2 style="margin-top: 0;">Create Account</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Enter your email to get started. We'll send you a verification link.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: rgba(248, 113, 113, 0.1); color: #f87171; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST">
            <input type="email" name="email" class="auth-input" placeholder="Email Address" required>
            <button type="submit" class="auth-btn">Verify Email</button>
        </form>

        <div class="divider"><span>OR</span></div>
        <a href="/auth/google" class="btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24" height="24">
            Sign up with Google
        </a>

        <p style="color: var(--text-muted);">
            Already have an account? <a href="/login" style="color: var(--primary);">Login</a>
        </p>
    </div>
</div>

</body>
</html>
