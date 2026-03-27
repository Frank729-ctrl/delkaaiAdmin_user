<?php
/**
 * Admin — IP Security.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/api.php';

require_admin();

$api = new DelkaiAPI(DELKAI_API_URL);

$blocked_ips = [];
$error       = null;
$success     = null;

// Handle unblock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip_address'])) {
    $ip = trim($_POST['ip_address'] ?? '');
    if (!$ip) {
        $error = 'IP address is required.';
    } else {
        try {
            $api->adminUnblockIp(DELKAI_MASTER_KEY, $ip);
            $success = "IP address {$ip} has been unblocked.";
        } catch (RuntimeException $e) {
            $error = 'Failed to unblock IP: ' . $e->getMessage();
        }
    }
}

// Load blocked IPs
try {
    $res         = $api->adminBlockedIps(DELKAI_MASTER_KEY);
    $blocked_ips = $res['blocked_ips'] ?? $res['data'] ?? $res;
    if (!is_array($blocked_ips)) $blocked_ips = [];
} catch (RuntimeException $e) {
    $error = $error ?? ('Failed to load blocked IPs: ' . $e->getMessage());
}

$active_page = 'security';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Security — DelkaAI Admin</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>IP Security</h1>
        <p>View and manage blocked IP addresses.</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error" data-autohide><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success" data-autohide><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="stat-grid" style="grid-template-columns:repeat(2,1fr);max-width:360px;margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-label">Blocked IPs</div>
        <div class="stat-value"><?= count($blocked_ips) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Status</div>
        <div class="stat-value" style="font-size:16px;">
          <?php if (count($blocked_ips) === 0): ?>
          <span class="badge badge-success">Clean</span>
          <?php else: ?>
          <span class="badge badge-warning">Threats</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Blocked IP Addresses</h2>
        <a href="/admin/security.php" class="btn btn-ghost btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.45"/></svg>
          Refresh
        </a>
      </div>

      <?php if (empty($blocked_ips)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <h3>No blocked IPs</h3>
        <p>All IPs are currently allowed. Suspicious IPs are blocked automatically by the honeypot middleware.</p>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>IP Address</th>
              <th>Reason</th>
              <th>Blocked At</th>
              <th>Expires</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($blocked_ips as $entry): ?>
            <?php
              // Handle both string IPs and object entries
              if (is_string($entry)) {
                $ip      = $entry;
                $reason  = '—';
                $blocked = null;
                $expires = null;
              } else {
                $ip      = htmlspecialchars($entry['ip_address'] ?? $entry['ip'] ?? '—');
                $reason  = htmlspecialchars($entry['reason']     ?? '—');
                $blocked = $entry['blocked_at'] ?? $entry['created_at'] ?? null;
                $expires = $entry['expires_at'] ?? null;
              }
            ?>
            <tr>
              <td><span class="mono"><?= $ip ?></span></td>
              <td class="text-muted" style="font-size:12px;"><?= $reason ?></td>
              <td class="text-muted" style="font-size:12px;">
                <?= $blocked ? date('M j, Y H:i', strtotime($blocked)) : '—' ?>
              </td>
              <td class="text-muted" style="font-size:12px;">
                <?= $expires ? date('M j, Y H:i', strtotime($expires)) : 'Permanent' ?>
              </td>
              <td>
                <form method="POST" action="/admin/security.php"
                      data-confirm="Unblock IP <?= htmlspecialchars(is_string($entry) ? $entry : ($entry['ip_address'] ?? '')) ?>?">
                  <input type="hidden" name="ip_address" value="<?= htmlspecialchars(is_string($entry) ? $entry : ($entry['ip_address'] ?? $entry['ip'] ?? '')) ?>">
                  <button type="submit" class="btn btn-secondary btn-xs">Unblock</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <div class="card mt-16">
      <div class="card-header"><h2>About IP Blocking</h2></div>
      <div class="card-body">
        <p class="text-muted" style="font-size:13px;line-height:1.7;">
          IP addresses are blocked automatically when the honeypot middleware detects suspicious probes
          (e.g., scanning for unknown routes). Blocked IPs receive a 404 response on all subsequent requests.
          Use the unblock action above to manually remove a block if it was triggered in error.
        </p>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
