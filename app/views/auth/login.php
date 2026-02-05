<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Great10</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: var(--surface);
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--border);
            text-align: center;
        }
        .auth-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
        }
        .auth-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .google-btn {
            width: 100%;
            padding: 12px;
            background: white;
            color: #333;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .divider {
            border-bottom: 1px solid var(--border);
            margin: 20px 0;
            position: relative;
        }
        .divider span {
            background: var(--surface);
            padding: 0 10px;
            color: var(--text-muted);
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2 style="margin-top: 0;">Welcome Back</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: rgba(248, 113, 113, 0.1); color: #f87171; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- We didn't build email/password login in AuthController yet, only Register/Google. 
             If user registers via email, they set a password. We need a doLogin method.
             Wait, the user requirement said "easy login system for all".
             I need to add doLogin to AuthController! 
             For now, I'll add the form assuming the backend will exist. -->

        <form action="/login" method="POST">
            <input type="email" name="email" class="auth-input" placeholder="Email Address" required>
            <input type="password" name="password" class="auth-input" placeholder="Password" required>
            <button type="submit" class="auth-btn">Login</button>
        </form>

        <div class="divider">OR</div>

        <a href="/auth/google" class="btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"> 
            Continue with Google
        </a>

        <p>Don't have an account? <a href="/register">Register</a></p>
    </div>
</div>

</body>
</html>
