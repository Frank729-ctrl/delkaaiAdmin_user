<?php
/**
 * Admin Dashboard.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/api.php';

require_admin();

$api = new DelkaiAPI(DELKAI_API_URL);

$health  = [];
$metrics = [];
$error   = null;

try {
    $health = $api->health();
} catch (RuntimeException $e) {
    $error = 'Health check failed: ' . $e->getMessage();
}

try {
    $metrics = $api->adminMetrics(DELKAI_MASTER_KEY);
} catch (RuntimeException $e) {
    $metrics_error = $e->getMessage();
}

$active_page = 'dashboard';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>Admin Dashboard</h1>
        <p>System overview and health status.</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- System health -->
    <?php
    $api_status   = $health['status']       ?? 'unknown';
    $groq_status  = $health['providers']['groq'] ?? 'unknown';
    $total_keys   = $metrics['total_keys']  ?? ($metrics['data']['total_keys'] ?? '—');
    $total_reqs   = $metrics['total_requests'] ?? ($metrics['data']['total_requests'] ?? '—');
    ?>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">API Status</div>
        <div class="stat-value" style="font-size:20px;">
          <?php if ($api_status === 'ok' || $api_status === 'healthy'): ?>
          <span class="badge badge-success" style="font-size:14px;padding:5px 12px;">Healthy</span>
          <?php else: ?>
          <span class="badge badge-error" style="font-size:14px;padding:5px 12px;"><?= htmlspecialchars($api_status) ?></span>
          <?php endif; ?>
        </div>
        <div class="stat-delta">live status</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Groq / LLM</div>
        <div class="stat-value" style="font-size:20px;">
          <?php if ($groq_status === 'ok' || $groq_status === 'healthy' || $groq_status === 'connected'): ?>
          <span class="badge badge-success" style="font-size:14px;padding:5px 12px;">Connected</span>
          <?php elseif ($groq_status === 'unknown'): ?>
          <span class="badge badge-neutral" style="font-size:14px;padding:5px 12px;">Unknown</span>
          <?php else: ?>
          <span class="badge badge-warning" style="font-size:14px;padding:5px 12px;"><?= htmlspecialchars($groq_status) ?></span>
          <?php endif; ?>
        </div>
        <div class="stat-delta">model provider</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Total API Keys</div>
        <div class="stat-value"><?= is_numeric($total_keys) ? number_format($total_keys) : $total_keys ?></div>
        <div class="stat-delta">across all platforms</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Total Requests</div>
        <div class="stat-value"><?= is_numeric($total_reqs) ? number_format($total_reqs) : $total_reqs ?></div>
        <div class="stat-delta">all time</div>
      </div>
    </div>

    <!-- Full health data -->
    <?php if (!empty($health)): ?>
    <div class="card mb-24">
      <div class="card-header"><h2>Health Response</h2></div>
      <div class="card-body">
        <pre class="code-block" style="min-height:unset;"><?= htmlspecialchars(json_encode($health, JSON_PRETTY_PRINT)) ?></pre>
      </div>
    </div>
    <?php endif; ?>

    <!-- Quick links -->
    <div class="card">
      <div class="card-header"><h2>Quick Actions</h2></div>
      <div class="card-body d-flex gap-12" style="flex-wrap:wrap;">
        <a href="/admin/keys" class="btn btn-secondary">Manage API Keys</a>
        <a href="/admin/metrics" class="btn btn-secondary">View Metrics</a>
        <a href="/admin/security" class="btn btn-secondary">Security</a>
        <a href="/admin/users" class="btn btn-secondary">Developers</a>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
