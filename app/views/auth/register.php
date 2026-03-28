<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Great10</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: var(--surface); padding: 40px; border-radius: 16px; width: 100%; max-width: 400px; border: 1px solid var(--border); text-align: center; }
        .auth-input { width: 100%; padding: 12px; margin-bottom: 15px; background: var(--bg); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-family: inherit; transition: border-color 0.2s; }
        .auth-input:focus { border-color: var(--primary); outline: none; }
        .auth-btn { width: 100%; padding: 12px; background: linear-gradient(135deg, var(--primary), #0ea5e9); color: #0f172a; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; margin-bottom: 20px; transition: transform 0.2s; }
        .auth-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(56, 189, 248, 0.3); }
        .btn-google { width: 100%; padding: 12px; background: white; color: #333; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; transition: transform 0.2s; }
        .btn-google:hover { transform: translateY(-2px); }
        .divider { border-bottom: 1px solid var(--border); margin: 20px 0; position: relative; }
        .divider span { background: var(--surface); padding: 0 10px; color: var(--text-muted); position: absolute; top: -10px; left: 50%; transform: translateX(-50%); font-size: 0.9rem; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; display: none; }
        .alert-error { background: rgba(248, 113, 113, 0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.3); }
        .alert-success { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74,222,128,0.3); }
        .otp-box { font-size: 2rem; letter-spacing: 5px; text-align: center; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card" id="registerPanel">
        <h2 style="margin-top: 0; font-size: 2rem;">Create Account</h2>
        <p style="color: var(--text-muted); margin-bottom: 25px;">Enter your details to get started.</p>

        <div id="regAlertBox" class="alert"></div>

        <form id="registerForm">
            <input type="email" name="email" class="auth-input" placeholder="Email Address" required>
            <input type="password" name="password" class="auth-input" placeholder="Create Password" minlength="6" required>
            
            <?php require_once '../core/Captcha.php'; echo Captcha::render(); ?>

            <button type="submit" id="regBtn" class="auth-btn">Register & Verify</button>
        </form>

        <div class="divider"><span>OR</span></div>
        
        <a href="/auth/google" class="btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24" height="24">
            Sign up with Google
        </a>

        <p style="margin-top: 25px; color: var(--text-muted);">
            Already have an account? <a href="/login" style="color: var(--primary); text-decoration: none; font-weight: 600;">Login</a>
        </p>
    </div>

    <!-- OTP Panel (Hidden initially) -->
    <div class="auth-card" id="otpPanel" style="display: none;">
        <h2 style="margin-top: 0; font-size: 2rem;">Verify Email</h2>
        <p style="color: var(--text-muted); margin-bottom: 25px;">We've sent a 6-digit verification code to your email. Please enter it below.</p>

        <div id="otpAlertBox" class="alert"></div>

        <form id="otpForm">
            <input type="text" name="otp" class="auth-input otp-box" placeholder="000000" maxlength="6" required autocomplete="off">
            <button type="submit" id="otpBtn" class="auth-btn">Verify & Complete</button>
        </form>
        
        <p style="margin-top: 25px; color: var(--text-muted);">
            <a href="#" onclick="location.reload()" style="color: var(--primary); text-decoration: none; font-weight: 600;">Back to Registration</a>
        </p>
    </div>
</div>

<script>
    // Handle URL param if redirecting from login
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('verify_pending')) {
        document.getElementById('registerPanel').style.display = 'none';
        document.getElementById('otpPanel').style.display = 'block';
        const alertBox = document.getElementById('otpAlertBox');
        alertBox.className = 'alert alert-error';
        alertBox.innerText = 'Please check your email for the OTP code to verify your account.';
        alertBox.style.display = 'block';
    }

    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('regBtn');
        const alertBox = document.getElementById('regAlertBox');
        
        btn.innerText = 'Processing...';
        btn.style.opacity = '0.7';
        btn.disabled = true;
        
        try {
            const formData = new FormData(e.target);
            const res = await fetch('/api/auth/register', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.status === 'success') {
                document.getElementById('registerPanel').style.display = 'none';
                document.getElementById('otpPanel').style.display = 'block';
                
                const otpAlert = document.getElementById('otpAlertBox');
                otpAlert.className = 'alert alert-success';
                otpAlert.innerText = data.message;
                otpAlert.style.display = 'block';
            } else {
                alertBox.className = 'alert alert-error';
                alertBox.innerText = data.message;
                alertBox.style.display = 'block';
            }
        } catch (err) {
            alertBox.className = 'alert alert-error';
            alertBox.innerText = 'Connection error. Check network and SMTP config.';
            alertBox.style.display = 'block';
        } finally {
            btn.innerText = 'Register & Verify';
            btn.style.opacity = '1';
            btn.disabled = false;
        }
    });

    document.getElementById('otpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('otpBtn');
        const alertBox = document.getElementById('otpAlertBox');
        
        btn.innerText = 'Verifying...';
        btn.disabled = true;
        
        try {
            const formData = new FormData(e.target);
            const res = await fetch('/api/auth/verify', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.status === 'success') {
                alertBox.className = 'alert alert-success';
                alertBox.innerText = data.message;
                alertBox.style.display = 'block';
                setTimeout(() => window.location.href = '/dashboard', 500);
            } else {
                alertBox.className = 'alert alert-error';
                alertBox.innerText = data.message;
                alertBox.style.display = 'block';
            }
        } catch (err) {
            alertBox.className = 'alert alert-error';
            alertBox.innerText = 'Connection error.';
            alertBox.style.display = 'block';
        } finally {
            btn.innerText = 'Verify & Complete';
            btn.disabled = false;
        }
    });

    <?php if (isset($_SESSION['error'])): ?>
        const aBox = document.getElementById('regAlertBox');
        aBox.className = 'alert alert-error';
        aBox.innerText = <?= json_encode($_SESSION['error']) ?>;
        aBox.style.display = 'block';
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>

</body>
</html>
