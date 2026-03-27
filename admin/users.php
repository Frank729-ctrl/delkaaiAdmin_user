<?php
/**
 * Admin — Developer accounts page.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$active_page = 'users';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Developers — DelkaAI Admin</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>Developer Accounts</h1>
        <p>Manage registered developer accounts.</p>
      </div>
    </div>

    <div class="alert alert-info">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <div>
        <strong>Managed via Supabase</strong><br>
        Developer accounts are managed through the Supabase dashboard. Use the Supabase admin console to view, deactivate, or delete developer accounts and their associated sessions.
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2>Developer Account Management</h2></div>
      <div class="card-body">
        <p class="text-muted" style="font-size:13px;margin-bottom:20px;">
          The following actions are available through the Supabase admin console:
        </p>
        <ul style="color:var(--muted);font-size:13px;line-height:2;padding-left:20px;">
          <li>View all registered developer accounts</li>
          <li>Deactivate or reactivate accounts</li>
          <li>Invalidate active sessions</li>
          <li>Delete accounts and associated data</li>
          <li>View login history and session metadata</li>
        </ul>

        <hr class="divider">

        <div class="d-flex gap-12" style="flex-wrap:wrap;">
          <a href="https://supabase.com/dashboard" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Open Supabase Dashboard
          </a>
        </div>
      </div>
    </div>

    <div class="card mt-16">
      <div class="card-header"><h2>API Key Association</h2></div>
      <div class="card-body">
        <p class="text-muted" style="font-size:13px;">
          Each developer's API keys are linked to their account email. To manage a developer's access,
          use the <a href="/admin/keys.php">API Keys</a> page to revoke their keys.
        </p>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
