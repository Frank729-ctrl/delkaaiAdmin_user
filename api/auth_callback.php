<?php
/**
 * Auth callback — exchanges a verified Clerk session for a DelkaAI session token.
 *
 * Clerk JS sets the __session cookie then redirects here.
 * PHP verifies the JWT, fetches user info, and provisions a DelkaAI session.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/clerk.php';
require_once __DIR__ . '/includes/api.php';

// Already has a valid DelkaAI session
if (get_session_token()) {
    header('Location: /dashboard');
    exit;
}

// Verify the Clerk __session JWT and get user info
$user = clerk_current_user();

if (!$user) {
    // JWT missing or invalid — show a small page that waits for Clerk JS to
    // establish the session (handles the case where the cookie isn't set yet).
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signing in… — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);">
  <p style="color:var(--muted);">Completing sign-in…</p>
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
  const user = window.Clerk.user;
  if (user) {
    // Session established — reload this page so PHP can read the cookie
    window.location.reload();
  } else {
    window.location.href = '/login';
  }
});
</script>
</body>
</html>
<?php
    exit;
}

// Exchange Clerk identity for a DelkaAI session token
$api    = new DelkaiAPI(DELKAI_API_URL);
$result = $api->clerkProvision(
    $user['email'],
    $user['full_name'] ?: ($user['email']),
    $user['id'],
    DELKAI_MASTER_KEY
);

if (!$result || empty($result['session_token'])) {
    header('Location: /login?error=1');
    exit;
}

set_session_token($result['session_token'], $result['expires_at'] ?? null);
header('Location: /dashboard');
exit;
