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
        .auth-card { background: var(--surface); padding: 40px; border-radius: 20px; width: 100%; max-width: 400px; border: 1px solid var(--border); text-align: center; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); }
        .social-btn { width: 100%; padding: 14px; color: #fff; border: none; border-radius: 12px; font-weight: 600; font-size: 1.05rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); margin-bottom: 15px; }
        .social-btn:hover { transform: translateY(-3px); }
        .btn-google { background: white; color: #333; }
        .btn-google:hover { box-shadow: 0 8px 25px rgba(255, 255, 255, 0.15); }
        .btn-facebook { background: #1877F2; }
        .btn-facebook:hover { box-shadow: 0 8px 25px rgba(24, 119, 242, 0.3); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; display: none; }
        .alert-error { background: rgba(248, 113, 113, 0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.3); }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2 style="margin-top: 0; font-size: 2.2rem; font-weight: 800;">Get Started</h2>
        <p style="color: var(--text-muted); margin-bottom: 35px;">Select an option to securely authenticate with Great10.</p>
        
        <div id="alertBox" class="alert"></div>

        <a href="/auth/google" class="social-btn btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24" height="24"> 
            Continue with Google
        </a>

        <a href="/auth/facebook" class="social-btn btn-facebook">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073C24 5.405 18.627 0 12 0C5.373 0 0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24V15.563H7.078V12.073H10.125V9.412C10.125 6.388 11.917 4.718 14.658 4.718C15.97 4.718 17.344 4.953 17.344 4.953V7.937H15.832C14.339 7.937 13.875 8.868 13.875 9.837V12.073H17.203L16.671 15.563H13.875V24C19.612 23.094 24 18.1 24 12.073Z"/></svg>
            Continue with Facebook
        </a>

        <p style="margin-top: 25px; color: var(--text-muted); font-size: 0.9rem;">
            By continuing, you are agreeing to our <a href="#" style="color: var(--primary);">Terms of Service</a>.
        </p>
    </div>
</div>

<script>
    <?php if (isset($_SESSION['error'])): ?>
        const aBox = document.getElementById('alertBox');
        aBox.className = 'alert alert-error';
        aBox.innerText = <?= json_encode($_SESSION['error']) ?>;
        aBox.style.display = 'block';
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>

</body>
</html>
