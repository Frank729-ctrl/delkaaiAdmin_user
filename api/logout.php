<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

$token = get_session_token();
if ($token) {
    try {
        $api = new DelkaiAPI(DELKAI_API_URL);
        $api->logout($token);
    } catch (RuntimeException $e) {
        // Ignore — still clear cookies
    }
    clear_session_token();
}

// Clear Clerk user-info cache cookies
setcookie('_clerk_uid',   '', time() - 3600, '/', '', true, true);
setcookie('_clerk_email', '', time() - 3600, '/', '', true, true);
setcookie('_clerk_name',  '', time() - 3600, '/', '', true, true);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signing out… — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);">
  <p style="color:var(--muted);">Signing out…</p>
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
  await window.Clerk.signOut();
  window.location.href = '/login';
});
</script>
</body>
</html>
