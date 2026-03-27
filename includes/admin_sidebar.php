<?php
/**
 * Admin sidebar partial.
 * Expects $active_page string to be set before including.
 */
$active_page = $active_page ?? '';
?>
<aside class="sidebar">
  <a href="/admin/dashboard.php" class="sidebar-logo">
    <div class="sidebar-logo-icon">D</div>
    <span class="sidebar-logo-text">DelkaAI Admin</span>
  </a>

  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Administration</div>

    <a href="/admin/dashboard.php" class="nav-link <?= $active_page === 'dashboard' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <a href="/admin/keys.php" class="nav-link <?= $active_page === 'keys' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
      API Keys
    </a>

    <a href="/admin/users.php" class="nav-link <?= $active_page === 'users' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Developers
    </a>

    <a href="/admin/metrics.php" class="nav-link <?= $active_page === 'metrics' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Metrics
    </a>

    <a href="/admin/security.php" class="nav-link <?= $active_page === 'security' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Security
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="/admin/logout.php">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Sign out
    </a>
  </div>
</aside>
