<?php
/**
 * Developer login — served by Clerk's embedded sign-in component.
 */
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
<title>Sign In — DelkaAI</title>
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
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--accent);
    color: #fff;
    font-weight: 700;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .clerk-logo-text {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
  }
  #clerk-sign-in {
    width: 100%;
    max-width: 400px;
  }
</style>
</head>
<body>

<div class="clerk-wrap">
  <a href="/" class="clerk-logo">
    <div class="clerk-logo-icon">D</div>
    <span class="clerk-logo-text">DelkaAI</span>
  </a>
  <div id="clerk-sign-in"></div>
</div>

<script
  async
  crossorigin="anonymous"
  data-clerk-publishable-key="<?= htmlspecialchars(CLERK_PUBLISHABLE_KEY) ?>"
  src="<?= htmlspecialchars(CLERK_FRONTEND_URL) ?>/npm/@clerk/clerk-js@latest/dist/clerk.browser.js"
  type="text/javascript">
</script>
<script>
window.addEventListener('load', async function () {
  await window.Clerk.load();
  window.Clerk.mountSignIn(document.getElementById('clerk-sign-in'), {
    afterSignInUrl: '/auth/callback',
    signUpUrl: '/register',
  });
});
</script>
</body>
</html>
