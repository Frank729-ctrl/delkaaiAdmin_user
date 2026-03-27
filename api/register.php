<?php
/**
 * Developer registration page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

// Already logged in → dashboard
if (get_session_token()) {
    header('Location: /dashboard');
    exit;
}

$api    = new DelkaiAPI(DELKAI_API_URL);
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $company   = trim($_POST['company']   ?? '') ?: null;

    if (!$full_name || !$email || !$password) {
        $error = 'Full name, email, and password are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            $api->register($email, $password, $full_name, $company);
            header('Location: /login?registered=1');
            exit;
        } catch (RuntimeException $e) {
            $code = $e->getCode();
            if ($code === 409) {
                $error = 'An account with that email already exists.';
            } else {
                $error = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">D</div>
      <span class="login-logo-text">DelkaAI</span>
    </div>

    <h1 class="login-title">Create your account</h1>
    <p class="login-subtitle">Start building with the DelkaAI API today.</p>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/register">
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name"
               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
               placeholder="Jane Smith" required autofocus>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="jane@example.com" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
          <input type="password" id="password" name="password"
                 placeholder="Min. 8 characters" required>
          <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <div class="form-group">
        <label for="company">Company <span class="optional">(optional)</span></label>
        <input type="text" id="company" name="company"
               value="<?= htmlspecialchars($_POST['company'] ?? '') ?>"
               placeholder="Acme Corp">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        Create Account
      </button>
    </form>

    <div class="login-footer">
      Already have an account? <a href="/login">Sign in</a>
    </div>
  </div>
</div>

<script src="/js/app.js"></script>
</body>
</html>
