<?php
/**
 * Admin — API Keys management.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/api.php';

require_admin();

$api = new DelkaiAPI(DELKAI_API_URL);

$keys       = [];
$error      = null;
$success    = null;
$new_keys   = null;

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $platform      = trim($_POST['platform']      ?? '');
        $owner         = trim($_POST['owner']         ?? '');
        $requires_hmac = isset($_POST['requires_hmac']);

        if (!$platform || !$owner) {
            $error = 'Platform and owner email are required.';
        } else {
            try {
                $new_keys = $api->adminCreateKey(DELKAI_MASTER_KEY, $platform, $owner, $requires_hmac);
                $success  = 'Key pair created successfully. Copy the values below — they will not be shown again.';
            } catch (RuntimeException $e) {
                $error = 'Failed to create key: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'revoke') {
        $prefix = trim($_POST['key_prefix'] ?? '');
        if (!$prefix) {
            $error = 'Key prefix is required.';
        } else {
            try {
                $api->adminRevokeKey(DELKAI_MASTER_KEY, $prefix);
                $success = 'Key revoked successfully.';
            } catch (RuntimeException $e) {
                $error = 'Failed to revoke key: ' . $e->getMessage();
            }
        }
    }
}

// Load keys
try {
    $res  = $api->adminKeys(DELKAI_MASTER_KEY);
    $keys = $res['keys'] ?? $res['data'] ?? $res;
    if (!is_array($keys)) $keys = [];
} catch (RuntimeException $e) {
    $error = $error ?? ('Failed to load keys: ' . $e->getMessage());
}

$active_page = 'keys';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API Keys — DelkaAI Admin</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>API Keys</h1>
        <p>Create, view, and revoke developer API key pairs.</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success" ><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- New key result -->
    <?php if ($new_keys): ?>
    <div class="new-key-box">
      <h3>New Key Pair Created</h3>
      <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Save these values now. The secret key (SK) cannot be retrieved later.</p>
      <?php foreach ($new_keys as $label => $value): ?>
      <?php if (is_string($value) && strlen($value) > 6): ?>
      <div class="key-row">
        <label><?= htmlspecialchars(strtoupper($label)) ?></label>
        <div class="key-value">
          <code><?= htmlspecialchars($value) ?></code>
          <button class="copy-btn" data-copy="<?= htmlspecialchars($value) ?>">Copy</button>
        </div>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Create key form -->
    <div class="card mb-24">
      <div class="card-header"><h2>Create New Key Pair</h2></div>
      <div class="card-body">
        <form method="POST" action="/admin/keys.php">
          <input type="hidden" name="action" value="create">
          <div class="form-row">
            <div class="form-group mb-0">
              <label for="platform">Platform / App Name</label>
              <input type="text" id="platform" name="platform" placeholder="e.g. iOS App, Web Dashboard" required>
            </div>
            <div class="form-group mb-0">
              <label for="owner">Owner Email</label>
              <input type="email" id="owner" name="owner" placeholder="developer@example.com" required>
            </div>
          </div>
          <div class="form-check mt-16 mb-16">
            <input type="checkbox" id="requires_hmac" name="requires_hmac">
            <label for="requires_hmac">Require HMAC signature</label>
          </div>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Key Pair
          </button>
        </form>
      </div>
    </div>

    <!-- Keys table -->
    <div class="card">
      <div class="card-header">
        <h2>All API Keys</h2>
        <span class="badge badge-neutral"><?= count($keys) ?> keys</span>
      </div>

      <?php if (empty($keys)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        <h3>No keys found</h3>
        <p>Create the first key pair above.</p>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Prefix</th>
              <th>Type</th>
              <th>Platform</th>
              <th>Owner</th>
              <th>HMAC</th>
              <th>Status</th>
              <th>Requests</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($keys as $key): ?>
            <?php
              $prefix   = htmlspecialchars($key['prefix']      ?? '');
              $type     = $key['key_type']     ?? '';
              $platform = htmlspecialchars($key['platform']    ?? '—');
              $owner    = htmlspecialchars($key['owner']       ?? '—');
              $hmac     = $key['requires_hmac'] ?? false;
              $active   = $key['is_active']     ?? false;
              $usage    = $key['usage_count']   ?? 0;
              $created  = $key['created_at']    ?? null;
            ?>
            <tr>
              <td><span class="mono"><?= $prefix ?>...</span></td>
              <td>
                <?php if ($type === 'pk'): ?>
                <span class="badge badge-blue">PK</span>
                <?php elseif ($type === 'sk'): ?>
                <span class="badge badge-accent">SK</span>
                <?php else: ?>
                <span class="badge badge-neutral"><?= htmlspecialchars(strtoupper($type)) ?></span>
                <?php endif; ?>
              </td>
              <td><?= $platform ?></td>
              <td class="text-muted" style="font-size:12px;"><?= $owner ?></td>
              <td><?= $hmac ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-neutral">No</span>' ?></td>
              <td>
                <?php if ($active): ?>
                <span class="badge badge-success">Active</span>
                <?php else: ?>
                <span class="badge badge-error">Revoked</span>
                <?php endif; ?>
              </td>
              <td class="mono"><?= number_format((int)$usage) ?></td>
              <td class="text-muted" style="font-size:12px;"><?= $created ? date('M j, Y', strtotime($created)) : '—' ?></td>
              <td>
                <?php if ($active): ?>
                <form method="POST" action="/admin/keys.php"
                      data-confirm="Revoke key <?= $prefix ?>? This cannot be undone.">
                  <input type="hidden" name="action" value="revoke">
                  <input type="hidden" name="key_prefix" value="<?= $prefix ?>">
                  <button type="submit" class="btn btn-danger btn-xs">Revoke</button>
                </form>
                <?php else: ?>
                <span class="text-muted" style="font-size:12px;">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
