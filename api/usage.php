<?php
/**
 * Developer Usage page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

$user  = require_auth();
$email = $user['sub'];
$api   = new DelkaiAPI(DELKAI_API_URL);
$keys  = [];
$error = null;

$rs = $user['rs'] ?? null;
if ($rs) {
    try {
        $keys = $api->keys($rs)['keys'] ?? [];
    } catch (RuntimeException $e) {
        if ($e->getCode() === 401) {
            $rs = $api->provision($email, $user['name'] ?? '', DELKAI_MASTER_KEY);
            if ($rs) {
                set_auth_cookie($email, $user['name'] ?? '', $user['company'] ?? null, $rs);
                try { $keys = $api->keys($rs)['keys'] ?? []; } catch (RuntimeException $e2) {}
            }
        }
    }
}

$total_reqs = array_sum(array_column($keys, 'usage_count'));
$active_page = 'usage';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usage — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>Usage</h1>
        <p>Request counts and activity breakdown for your API keys.</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);max-width:660px;margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-label">Total Requests</div>
        <div class="stat-value"><?= number_format($total_reqs) ?></div>
        <div class="stat-delta">all time</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Keys Tracked</div>
        <div class="stat-value"><?= count($keys) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Avg per Key</div>
        <div class="stat-value"><?= count($keys) > 0 ? number_format(round($total_reqs / count($keys))) : '—' ?></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Usage Breakdown by Key</h2>
      </div>

      <?php if (empty($keys)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        <h3>No usage data</h3>
        <p>Usage will appear here once you start making API requests.</p>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Key Prefix</th>
              <th>Type</th>
              <th>Platform</th>
              <th>Status</th>
              <th>Request Count</th>
              <th>Last Used</th>
              <th>Share</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($keys as $key): ?>
            <?php
              $usage = (int)($key['usage_count'] ?? 0);
              $pct   = $total_reqs > 0 ? round(($usage / $total_reqs) * 100) : 0;
              $last  = $key['last_used'] ?? null;
              $type  = $key['key_type'] ?? '';
            ?>
            <tr>
              <td><span class="mono"><?= htmlspecialchars($key['raw_prefix'] ?? $key['prefix'] ?? '') ?>...</span></td>
              <td>
                <?php if ($type === 'pk'): ?>
                <span class="badge badge-blue">PK</span>
                <?php elseif ($type === 'sk'): ?>
                <span class="badge badge-accent">SK</span>
                <?php else: ?>
                <span class="badge badge-neutral"><?= htmlspecialchars(strtoupper($type)) ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($key['platform'] ?? '—') ?></td>
              <td>
                <?php if ($key['is_active'] ?? false): ?>
                <span class="badge badge-success">Active</span>
                <?php else: ?>
                <span class="badge badge-error">Inactive</span>
                <?php endif; ?>
              </td>
              <td class="mono"><?= number_format($usage) ?></td>
              <td class="text-muted" style="font-size:12px;">
                <?= $last ? date('M j, Y H:i', strtotime($last)) : 'Never' ?>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="background:var(--surface2);border-radius:100px;height:6px;width:80px;overflow:hidden;">
                    <div style="background:var(--accent);height:100%;width:<?= $pct ?>%;border-radius:100px;"></div>
                  </div>
                  <span class="text-muted" style="font-size:12px;"><?= $pct ?>%</span>
                </div>
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
