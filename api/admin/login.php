<?php
/**
 * Admin login page.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    header('Location: /admin/dashboard');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        set_admin_session();
        header('Location: /admin/dashboard');
        exit;
    } else {
        $error = 'Invalid credentials.';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">D</div>
      <span class="login-logo-text">DelkaAI Admin</span>
    </div>

    <h1 class="login-title">Admin Access</h1>
    <p class="login-subtitle">Sign in with your administrator credentials.</p>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/admin/login">
      <div class="form-group">
        <label for="email">Admin Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="admin@yourdomain.com" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
          <input type="password" id="password" name="password" required placeholder="Admin password">
          <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        Sign In as Admin
      </button>
    </form>

    <div class="login-footer">
      <a href="/" style="color:var(--muted);">← Back to site</a>
    </div>
  </div>
</div>

<script src="/js/app.js"></script>
</body>
</html>
