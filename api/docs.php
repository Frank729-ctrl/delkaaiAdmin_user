<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

$active_page = 'docs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentation — DelkaAI</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>
  <main class="content">
    <div class="page-header">
      <h1>API Documentation</h1>
    </div>

    <div class="card" style="margin-bottom:1.5rem">
      <h2 style="margin-bottom:.75rem">Base URL</h2>
      <code class="mono" style="background:var(--surface2);padding:.5rem 1rem;border-radius:6px;display:block"><?= htmlspecialchars(DELKAI_API_URL) ?></code>
    </div>

    <div class="card" style="margin-bottom:1.5rem">
      <h2 style="margin-bottom:.75rem">Authentication</h2>
      <p style="color:var(--muted);margin-bottom:1rem">All API requests require your Secret Key sent in the request header:</p>
      <pre class="mono" style="background:var(--surface2);padding:1rem;border-radius:6px;overflow-x:auto">X-DelkaAI-Key: sk_live_your_secret_key</pre>
    </div>

    <div class="card" style="margin-bottom:1.5rem">
      <h2 style="margin-bottom:1rem">Endpoints</h2>

      <div style="border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">
        <div style="background:var(--surface2);padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem">
          <span class="badge badge-success">POST</span>
          <code class="mono">/v1/cv/generate</code>
          <span style="color:var(--muted);margin-left:auto">Generate a CV</span>
        </div>
        <div style="padding:1rem">
          <p style="color:var(--muted);margin-bottom:.75rem">Generate a professional CV from raw text input.</p>
          <strong style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)">Request Body</strong>
          <pre class="mono" style="background:var(--surface2);padding:.75rem;border-radius:6px;margin-top:.5rem;overflow-x:auto;font-size:.85rem">{
  "raw_text": "John Doe, Software Engineer with 5 years experience in Python...",
  "platform": "your-platform-name"
}</pre>
        </div>
      </div>

      <div style="border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">
        <div style="background:var(--surface2);padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem">
          <span class="badge badge-success">POST</span>
          <code class="mono">/v1/cover-letter/generate</code>
          <span style="color:var(--muted);margin-left:auto">Generate a cover letter</span>
        </div>
        <div style="padding:1rem">
          <p style="color:var(--muted);margin-bottom:.75rem">Generate a tailored cover letter for a job application.</p>
          <strong style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)">Request Body</strong>
          <pre class="mono" style="background:var(--surface2);padding:.75rem;border-radius:6px;margin-top:.5rem;overflow-x:auto;font-size:.85rem">{
  "cv_text": "Your CV content here...",
  "job_description": "Job posting text...",
  "platform": "your-platform-name"
}</pre>
        </div>
      </div>

      <div style="border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">
        <div style="background:var(--surface2);padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem">
          <span class="badge badge-success">POST</span>
          <code class="mono">/v1/support/chat</code>
          <span style="color:var(--muted);margin-left:auto">Support chat</span>
        </div>
        <div style="padding:1rem">
          <p style="color:var(--muted);margin-bottom:.75rem">Streaming AI support chat response.</p>
          <pre class="mono" style="background:var(--surface2);padding:.75rem;border-radius:6px;margin-top:.5rem;overflow-x:auto;font-size:.85rem">{
  "session_id": "unique-session-id",
  "message": "How do I format my CV?",
  "platform": "your-platform-name"
}</pre>
        </div>
      </div>

      <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="background:var(--surface2);padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem">
          <span class="badge badge-neutral">GET</span>
          <code class="mono">/v1/health</code>
          <span style="color:var(--muted);margin-left:auto">Health check</span>
        </div>
        <div style="padding:1rem">
          <p style="color:var(--muted)">Check API availability and provider status. No authentication required.</p>
        </div>
      </div>
    </div>

    <div class="card">
      <h2 style="margin-bottom:.75rem">Example Request</h2>
      <pre class="mono" style="background:var(--surface2);padding:1rem;border-radius:6px;overflow-x:auto;font-size:.85rem">curl -X POST <?= htmlspecialchars(DELKAI_API_URL) ?>/v1/cv/generate \
  -H "X-DelkaAI-Key: sk_live_your_secret_key" \
  -H "Content-Type: application/json" \
  -d '{"raw_text": "Jane Doe, Python developer...", "platform": "myapp"}'</pre>
    </div>
  </main>
</div>
<script src="/js/app.js"></script>
</body>
</html>
