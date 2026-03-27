<?php
/**
 * Admin — System Metrics.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/api.php';

require_admin();

$api     = new DelkaiAPI(DELKAI_API_URL);
$metrics = [];
$error   = null;

try {
    $metrics = $api->adminMetrics(DELKAI_MASTER_KEY);
} catch (RuntimeException $e) {
    $error = 'Failed to load metrics: ' . $e->getMessage();
}

// Flatten data key if present
$data = $metrics['data'] ?? $metrics;

$active_page = 'metrics';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Metrics — DelkaAI Admin</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>System Metrics</h1>
        <p>Platform-wide usage statistics and performance data.</p>
      </div>
      <div class="page-header-actions">
        <a href="/admin/metrics.php" class="btn btn-ghost btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.45"/></svg>
          Refresh
        </a>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($data)): ?>

    <!-- Stat cards from known metric fields -->
    <div class="stat-grid">
      <?php
      $stat_map = [
        'total_keys'         => ['label' => 'Total Keys',        'icon' => 'purple'],
        'active_keys'        => ['label' => 'Active Keys',       'icon' => 'green'],
        'total_requests'     => ['label' => 'Total Requests',    'icon' => 'blue'],
        'avg_response_ms'    => ['label' => 'Avg Response',      'icon' => 'yellow', 'suffix' => 'ms'],
        'blocked_ips'        => ['label' => 'Blocked IPs',       'icon' => 'red'],
        'requests_today'     => ['label' => 'Requests Today',    'icon' => 'blue'],
        'flagged_keys'       => ['label' => 'Flagged Keys',      'icon' => 'yellow'],
        'total_developers'   => ['label' => 'Developers',        'icon' => 'purple'],
      ];
      $shown = 0;
      foreach ($stat_map as $key => $cfg):
        if (!isset($data[$key])) continue;
        $val = $data[$key];
        if (is_numeric($val)) $val = number_format(round($val)) . ($cfg['suffix'] ?? '');
        $shown++;
      ?>
      <div class="stat-card">
        <div class="stat-label"><?= $cfg['label'] ?></div>
        <div class="stat-value"><?= htmlspecialchars((string)$val) ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($shown === 0): ?>
    <div class="alert alert-info mb-24">No known metric fields found. See raw data below.</div>
    <?php endif; ?>

    <!-- Full JSON dump -->
    <div class="card">
      <div class="card-header"><h2>Raw Metrics Response</h2></div>
      <div class="card-body">
        <pre class="code-block" style="min-height:unset;"><?= htmlspecialchars(json_encode($metrics, JSON_PRETTY_PRINT)) ?></pre>
      </div>
    </div>

    <?php elseif (!$error): ?>
    <div class="empty-state" style="margin-top:48px;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      <h3>No metrics data</h3>
      <p>The metrics endpoint returned an empty response.</p>
    </div>
    <?php endif; ?>

  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
