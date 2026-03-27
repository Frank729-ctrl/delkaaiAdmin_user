<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (get_session_token()) {
    header('Location: /dashboard');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
<style>
.clerk-wrap {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: var(--bg);
}
.clerk-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 32px;
  text-decoration: none;
}
.clerk-logo-icon {
  width: 36px; height: 36px;
  border-radius: 8px;
  background: var(--accent);
  color: #fff;
  font-weight: 700;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.clerk-logo-text { font-size: 18px; font-weight: 700; color: var(--text); }
#clerk-sign-up { width: 100%; max-width: 400px; }
</style>
</head>
<body>
<div class="clerk-wrap">
  <a href="/" class="clerk-logo">
    <div class="clerk-logo-icon">D</div>
    <span class="clerk-logo-text">DelkaAI</span>
  </a>
  <div id="clerk-sign-up"></div>
</div>

<!-- Clerk JS from jsDelivr (reliable CDN, pinned to v5) -->
<script src="https://cdn.jsdelivr.net/npm/@clerk/clerk-js@5/dist/clerk.browser.js"
        crossorigin="anonymous"></script>
<script>
(async function () {
  const clerk = new window.Clerk('<?= htmlspecialchars(CLERK_PUBLISHABLE_KEY) ?>');
  await clerk.load();

  // If already signed in, go straight to callback
  if (clerk.user) {
    window.location.href = '/auth/callback';
    return;
  }

  clerk.mountSignUp(document.getElementById('clerk-sign-up'), {
    signInUrl: '/login',
  });

  // Redirect to auth callback when session created
  clerk.addListener(function (resources) {
    if (resources.session) {
      window.location.href = '/auth/callback';
    }
  });
}());
</script>
</body>
</html>
