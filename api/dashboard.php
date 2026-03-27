<?php
/**
 * Developer Dashboard — Overview.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

require_auth();

$api   = new DelkaiAPI(DELKAI_API_URL);
$token = get_session_token();

$me       = [];
$overview = [];
$error    = null;

try {
    $me       = $api->me($token);
    $overview = $api->overview($token);
} catch (RuntimeException $e) {
    if ($e->getCode() === 401) {
        clear_session_token();
        header('Location: /login');
        exit;
    }
    $error = $e->getMessage();
}

$first_name   = explode(' ', $me['full_name'] ?? 'Developer')[0];
$total_keys   = $overview['total_keys']      ?? 0;
$active_keys  = $overview['active_keys']     ?? 0;
$total_reqs   = $overview['total_requests']  ?? 0;
$avg_resp     = $overview['avg_response_ms'] ?? null;

$active_page = 'overview';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Overview — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>Welcome back, <?= htmlspecialchars($first_name) ?></h1>
        <p>Here's an overview of your DelkaAI usage.</p>
      </div>
      <div class="page-header-actions">
        <a href="/keys" class="btn btn-primary btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
          View API Keys
        </a>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon purple">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        </div>
        <div class="stat-label">Total Keys</div>
        <div class="stat-value"><?= $total_keys ?></div>
        <div class="stat-delta"><?= $active_keys ?> active</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-label">Active Keys</div>
        <div class="stat-value"><?= $active_keys ?></div>
        <div class="stat-delta">of <?= $total_keys ?> total</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon blue">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-label">Total Requests</div>
        <div class="stat-value"><?= number_format($total_reqs) ?></div>
        <div class="stat-delta">all time</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon yellow">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-label">Avg Response</div>
        <div class="stat-value"><?= $avg_resp !== null ? round($avg_resp) . 'ms' : '—' ?></div>
        <div class="stat-delta">average latency</div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="card mb-24">
      <div class="card-header">
        <h2>Quick Actions</h2>
      </div>
      <div class="card-body d-flex gap-12" style="flex-wrap:wrap;">
        <a href="/keys" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
          Manage Keys
        </a>
        <a href="/docs" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          View Docs
        </a>
        <a href="/playground" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          Try Playground
        </a>
        <a href="/usage" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          View Usage
        </a>
      </div>
    </div>

    <!-- Account info -->
    <div class="card">
      <div class="card-header"><h2>Account Details</h2></div>
      <div class="card-body">
        <table class="data-table">
          <tbody>
            <tr>
              <td style="width:160px;color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Name</td>
              <td><?= htmlspecialchars($me['full_name'] ?? '—') ?></td>
            </tr>
            <tr>
              <td style="color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Email</td>
              <td><?= htmlspecialchars($me['email'] ?? '—') ?></td>
            </tr>
            <tr>
              <td style="color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Company</td>
              <td><?= htmlspecialchars($me['company'] ?? '—') ?></td>
            </tr>
            <tr>
              <td style="color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Status</td>
              <td>
                <?php if ($me['is_active'] ?? false): ?>
                <span class="badge badge-success">Active</span>
                <?php else: ?>
                <span class="badge badge-error">Inactive</span>
                <?php endif; ?>
              </td>
            </tr>
            <tr>
              <td style="color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Member Since</td>
              <td class="text-muted"><?= isset($me['created_at']) ? date('F j, Y', strtotime($me['created_at'])) : '—' ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
