<?php
/**
 * Developer API Keys — self-service key creation and management.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

$user  = require_auth();
$email = $user['sub'];
$api   = new DelkaiAPI(DELKAI_API_URL);
$keys  = [];
$error = null;
$new_key = null;

// ── Handle create ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $key_name = trim($_POST['key_name'] ?? '');
    if (!$key_name) {
        $error = 'Please enter a name for your API key.';
    } else {
        try {
            $new_key = $api->developerCreateKey($email, $key_name);
        } catch (RuntimeException $e) {
            $error = 'Failed to create key: ' . $e->getMessage();
        }
    }
}

// ── Handle revoke ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'revoke') {
    $prefix = trim($_POST['key_prefix'] ?? '');
    if ($prefix) {
        try {
            $api->developerRevokeKey($email, $prefix);
        } catch (RuntimeException $e) {
            $error = 'Failed to revoke key: ' . $e->getMessage();
        }
    }
}

// ── Load keys ─────────────────────────────────────────────────────────────────
try {
    $keys = $api->developerKeys($email);
} catch (RuntimeException $e) {
    $error = $error ?? 'Could not load keys.';
}

$active_page = 'keys';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API Keys — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
<style>
.keys-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.keys-header h1 { font-size:22px; font-weight:700; margin:0; }
.keys-warn { font-size:13px; color:var(--muted); margin-top:4px; }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:32px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.3); }
.modal h2 { font-size:18px; font-weight:700; margin:0 0 8px; }
.modal p { font-size:13px; color:var(--muted); margin:0 0 24px; line-height:1.5; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.modal-close { position:absolute; top:16px; right:16px; background:none; border:none; cursor:pointer; color:var(--muted); padding:4px; }

/* New key reveal */
.key-reveal { background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:24px; }
.key-reveal-header { display:flex; align-items:center; gap:8px; margin-bottom:12px; }
.key-reveal-title { font-weight:600; font-size:14px; }
.key-reveal-warn { font-size:12px; color:#f59e0b; display:flex; align-items:center; gap:6px; margin-bottom:16px; }
.key-reveal-row { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
.key-reveal-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); width:28px; }
.key-reveal-val { font-family:monospace; font-size:12px; background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:8px 12px; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Key table */
.key-name { font-weight:500; font-size:13px; }
.key-prefix { font-family:monospace; font-size:12px; color:var(--muted); }
</style>
</head>
<body>
<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <main class="content">

    <div class="keys-header">
      <div>
        <h1>API Keys</h1>
        <p class="keys-warn">Keep your secret keys safe. Do not share them or commit them to source control.</p>
      </div>
      <button class="btn btn-primary" onclick="document.getElementById('create-modal').classList.add('open')">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create API Key
      </button>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($new_key): ?>
    <div class="key-reveal">
      <div class="key-reveal-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span class="key-reveal-title">API Key Created — <?= htmlspecialchars($new_key['platform'] ?? 'New Key') ?></span>
      </div>
      <div class="key-reveal-warn">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Save your secret key now — it will not be shown again.
      </div>
      <?php if (!empty($new_key['secret_key'])): ?>
      <div class="key-reveal-row">
        <span class="key-reveal-label">SK</span>
        <span class="key-reveal-val" id="new-sk"><?= htmlspecialchars($new_key['secret_key']) ?></span>
        <button class="copy-btn btn btn-secondary btn-xs" data-copy="<?= htmlspecialchars($new_key['secret_key']) ?>">Copy</button>
      </div>
      <?php endif; ?>
      <?php if (!empty($new_key['publishable_key'])): ?>
      <div class="key-reveal-row">
        <span class="key-reveal-label">PK</span>
        <span class="key-reveal-val"><?= htmlspecialchars($new_key['publishable_key']) ?></span>
        <button class="copy-btn btn btn-secondary btn-xs" data-copy="<?= htmlspecialchars($new_key['publishable_key']) ?>">Copy</button>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Keys table -->
    <div class="card">
      <div class="card-header">
        <h2>Your API Keys</h2>
        <span class="badge badge-neutral"><?= count($keys) ?> / 10</span>
      </div>

      <?php if (empty($keys)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        <h3>No API keys yet</h3>
        <p>Create your first API key to start making requests.</p>
        <button class="btn btn-primary" onclick="document.getElementById('create-modal').classList.add('open')">Create API Key</button>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Prefix</th>
              <th>Status</th>
              <th>Requests</th>
              <th>Last Used</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($keys as $key):
              $prefix   = htmlspecialchars($key['raw_prefix'] ?? $key['prefix'] ?? '');
              $type     = $key['key_type']    ?? '';
              $platform = htmlspecialchars($key['platform']   ?? '—');
              $active   = $key['is_active']   ?? false;
              $usage    = $key['usage_count'] ?? 0;
              $lastUsed = $key['last_used_at'] ?? $key['last_used'] ?? null;
              $created  = $key['created_at']  ?? null;
            ?>
            <tr>
              <td><span class="key-name"><?= $platform ?></span></td>
              <td>
                <?php if ($type === 'sk'): ?>
                <span class="badge badge-accent">Secret</span>
                <?php elseif ($type === 'pk'): ?>
                <span class="badge badge-blue">Public</span>
                <?php else: ?>
                <span class="badge badge-neutral"><?= strtoupper($type) ?></span>
                <?php endif; ?>
              </td>
              <td><span class="key-prefix"><?= $prefix ?>...</span></td>
              <td>
                <?= $active
                  ? '<span class="badge badge-success">Active</span>'
                  : '<span class="badge badge-error">Revoked</span>' ?>
              </td>
              <td class="mono"><?= number_format((int)$usage) ?></td>
              <td class="text-muted" style="font-size:12px;"><?= $lastUsed ? date('M j, Y', strtotime($lastUsed)) : '—' ?></td>
              <td class="text-muted" style="font-size:12px;"><?= $created ? date('M j, Y', strtotime($created)) : '—' ?></td>
              <td>
                <?php if ($active && $type === 'sk'): ?>
                <form method="POST" action="/keys"
                      data-confirm="Revoke this key? Apps using it will stop working immediately.">
                  <input type="hidden" name="action" value="revoke">
                  <input type="hidden" name="key_prefix" value="<?= $prefix ?>">
                  <button type="submit" class="btn btn-danger btn-xs">Revoke</button>
                </form>
                <?php else: ?>—<?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- Usage guide -->
    <div class="card mt-16">
      <div class="card-header"><h2>Using your API key</h2></div>
      <div class="card-body">
        <p class="text-muted mb-12" style="font-size:13px;">
          Pass your Secret Key (SK) in the <code style="background:var(--surface2);padding:1px 6px;border-radius:4px;font-size:12px;">X-DelkaAI-Key</code> header with every request.
        </p>
        <div class="code-block">curl <?= htmlspecialchars(DELKAI_API_URL) ?>/v1/health \
  -H <span style="color:#86efac;">"X-DelkaAI-Key: sk_live_your_secret_key"</span></div>
        <p class="text-muted mt-12" style="font-size:13px;">
          See the <a href="/docs" style="color:var(--accent)">full API documentation</a> for request formats and response examples.
        </p>
      </div>
    </div>

  </main>
</div>

<!-- Create key modal -->
<div class="modal-overlay" id="create-modal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal" style="position:relative;">
    <button class="modal-close" onclick="document.getElementById('create-modal').classList.remove('open')" title="Close">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <h2>Create API Key</h2>
    <p>Give your key a descriptive name — for example, the app or project that will use it.</p>
    <form method="POST" action="/keys">
      <input type="hidden" name="action" value="create">
      <div class="form-group">
        <label for="key_name">Key Name</label>
        <input type="text" id="key_name" name="key_name"
               placeholder="e.g. Production App, iOS Client"
               maxlength="80" required autofocus>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost"
                onclick="document.getElementById('create-modal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Key</button>
      </div>
    </form>
  </div>
</div>

<script src="/js/app.js"></script>
<script>
<?php if ($error && str_contains($error ?? '', 'name')): ?>
document.getElementById('create-modal').classList.add('open');
<?php endif; ?>
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') document.getElementById('create-modal').classList.remove('open');
});
</script>
</body>
</html>
