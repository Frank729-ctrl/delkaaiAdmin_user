<?php
/**
 * Auth callback.
 * Clerk JS redirects here after sign-in. The __session cookie is already set.
 * PHP verifies the session via Clerk's Backend API, then provisions a DelkaAI session.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/clerk.php';
require_once __DIR__ . '/includes/api.php';

// Already authenticated with DelkaAI — skip straight to dashboard
if (get_session_token()) {
    header('Location: /dashboard');
    exit;
}

// __session cookie not present yet — Clerk JS needs to finish setting up.
// Show a minimal page that loads Clerk JS and waits for the session,
// then posts back here once the cookie is in place.
if (empty($_COOKIE['__session'])) {
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signing in… — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;flex-direction:column;align-items:center;
            justify-content:center;gap:16px;background:var(--bg);">
  <div style="width:32px;height:32px;border:3px solid var(--border);
              border-top-color:var(--accent);border-radius:50%;animation:spin 0.8s linear infinite;"></div>
  <p style="color:var(--muted);font-size:14px;">Completing sign-in…</p>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
<script src="https://cdn.jsdelivr.net/npm/@clerk/clerk-js@5/dist/clerk.browser.js" crossorigin="anonymous"></script>
<script>
(async function () {
  try {
    const clerk = new window.Clerk('<?= htmlspecialchars(CLERK_PUBLISHABLE_KEY) ?>');
    await clerk.load();
    if (clerk.session) {
      // Cookie should now be set — reload so PHP can read it
      window.location.reload();
    } else {
      // No active session — back to login
      window.location.href = '/login';
    }
  } catch (e) {
    window.location.href = '/login';
  }
}());
</script>
</body>
</html>
<?php
    exit;
}

// __session cookie is present — verify it via Clerk's Backend API
$user = clerk_current_user();

if (!$user) {
    // Verification failed (expired, revoked, or invalid) — send back to login
    header('Location: /login');
    exit;
}

// Exchange Clerk identity for a 24 h DelkaAI session token
$api    = new DelkaiAPI(DELKAI_API_URL);
$result = $api->clerkProvision(
    $user['email'],
    $user['full_name'] ?: $user['email'],
    $user['id'],
    DELKAI_MASTER_KEY
);

if (!$result || empty($result['session_token'])) {
    // Backend unreachable or provision failed
    header('Location: /login?error=provision');
    exit;
}

set_session_token($result['session_token'], $result['expires_at'] ?? null);
header('Location: /dashboard');
exit;
