<?php
$pageTitle = "Page Not Found - Great10";
ob_start();
?>

<div class="error-container">
    <div class="glass-panel error-content">
        <h1 class="text-gradient">404</h1>
        <h2>Lost in the Void?</h2>
        <p>The page you are looking for has drifted into deep space or never existed.</p>
        
        <div class="action-buttons">
            <a href="/" class="btn btn-primary">Return to Base</a>
            <a href="/movies" class="btn btn-secondary">Browse Movies</a>
        </div>
    </div>
</div>

<style>
.error-container {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
}

.error-content {
    max-width: 500px;
    padding: 60px 40px;
    animation: float 6s ease-in-out infinite;
}

.error-content h1 {
    font-size: 8rem;
    font-weight: 800;
    margin: 0;
    line-height: 1;
    opacity: 0.8;
}

.error-content h2 {
    font-size: 2rem;
    margin: 20px 0 10px;
}

.error-content p {
    color: var(--text-muted);
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-secondary {
    background: rgba(255,255,255,0.1);
    color: white;
    transition: background 0.2s;
}

.btn-secondary:hover {
    background: rgba(255,255,255,0.2);
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

@media (max-width: 768px) {
    .error-content h1 { font-size: 5rem; }
}
</style>

<?php
$content = ob_get_clean();
require_once '../app/views/layout.php';
?>
