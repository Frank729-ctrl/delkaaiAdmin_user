<?php
/**
 * Developer API Keys page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

require_auth();

$api   = new DelkaiAPI(DELKAI_API_URL);
$token = get_session_token();

$keys  = [];
$error = null;

try {
    $res  = $api->keys($token);
    $keys = $res['keys'] ?? [];
} catch (RuntimeException $e) {
    if ($e->getCode() === 401) {
        clear_session_token();
        header('Location: /login.php');
        exit;
    }
    $error = $e->getMessage();
}

$active_keys = count(array_filter($keys, fn($k) => $k['is_active'] ?? false));

$active_page = 'keys';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API Keys — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>API Keys</h1>
        <p>Your authentication credentials for accessing the DelkaAI API.</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="stat-grid" style="grid-template-columns:repeat(2,1fr);max-width:440px;margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-label">Total Keys</div>
        <div class="stat-value"><?= count($keys) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Active Keys</div>
        <div class="stat-value"><?= $active_keys ?></div>
      </div>
    </div>

    <div class="alert alert-info" style="margin-bottom:20px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>To create new key pairs, please contact the administrator. Keys are provisioned per platform/application.</span>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Your Keys</h2>
        <span class="badge badge-neutral"><?= count($keys) ?> total</span>
      </div>

      <?php if (empty($keys)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        <h3>No API keys yet</h3>
        <p>Contact the administrator to get your first API key pair.</p>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Prefix</th>
              <th>Type</th>
              <th>Platform</th>
              <th>Status</th>
              <th>Requests</th>
              <th>Last Used</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($keys as $key): ?>
            <?php
              $prefix   = htmlspecialchars($key['prefix']      ?? '');
              $type     = $key['key_type']   ?? '';
              $platform = htmlspecialchars($key['platform']    ?? '—');
              $active   = $key['is_active']  ?? false;
              $usage    = $key['usage_count'] ?? 0;
              $lastUsed = $key['last_used']   ?? null;
              $created  = $key['created_at']  ?? null;
            ?>
            <tr>
              <td>
                <span class="mono"><?= $prefix ?>...</span>
              </td>
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
              <td>
                <?php if ($active): ?>
                <span class="badge badge-success">Active</span>
                <?php else: ?>
                <span class="badge badge-error">Inactive</span>
                <?php endif; ?>
              </td>
              <td class="mono"><?= number_format($usage) ?></td>
              <td class="text-muted" style="font-size:12px;">
                <?= $lastUsed ? date('M j, Y H:i', strtotime($lastUsed)) : '—' ?>
              </td>
              <td class="text-muted" style="font-size:12px;">
                <?= $created ? date('M j, Y', strtotime($created)) : '—' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <div class="card mt-16">
      <div class="card-header"><h2>How to use your keys</h2></div>
      <div class="card-body">
        <p class="text-muted mb-8" style="font-size:13px;">Pass your API key in the <code style="background:var(--surface2);padding:1px 6px;border-radius:4px;font-size:12px;">X-DelkaAI-Key</code> header with every request.</p>
        <div class="code-block">
<span class="c-method">curl</span> <span class="c-url">https://delka.onrender.com/v1/health</span> \
  -H <span class="c-str">"X-DelkaAI-Key: YOUR_API_KEY"</span>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
