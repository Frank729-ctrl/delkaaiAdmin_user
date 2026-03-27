<?php
/**
 * Developer login page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

if (get_session_token()) {
    header('Location: /dashboard.php');
    exit;
}

$api   = new DelkaiAPI(DELKAI_API_URL);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        $result = $api->loginFull($email, $password);
        if ($result && !empty($result['session_token'])) {
            set_session_token($result['session_token'], $result['expires_at'] ?? null);
            header('Location: /dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$registered = isset($_GET['registered']) && $_GET['registered'] === '1';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">D</div>
      <span class="login-logo-text">DelkaAI</span>
    </div>

    <h1 class="login-title">Welcome back</h1>
    <p class="login-subtitle">Sign in to your developer console.</p>

    <?php if ($registered): ?>
    <div class="alert alert-success" data-autohide>
      Account created! You can now sign in.
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/login.php">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="jane@example.com" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
          <input type="password" id="password" name="password"
                 placeholder="Your password" required>
          <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        Sign In
      </button>
    </form>

    <div class="login-footer">
      Don't have an account? <a href="/register.php">Create one</a>
    </div>
  </div>
</div>

<script src="/js/app.js"></script>
</body>
</html>
