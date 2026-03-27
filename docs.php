<?php
/**
 * API Documentation page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

$active_page = 'docs';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Documentation — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>API Documentation</h1>
        <p>Reference guide for all DelkaAI endpoints.</p>
      </div>
    </div>

    <!-- Base URL -->
    <div class="card mb-24" id="base-url">
      <div class="card-header"><h2>Base URL</h2></div>
      <div class="card-body">
        <div class="key-value" style="max-width:420px;">
          <code>https://delka.onrender.com</code>
          <button class="copy-btn" data-copy="https://delka.onrender.com">Copy</button>
        </div>
        <p class="form-hint mt-16">All endpoints are prefixed with <code style="background:var(--surface2);padding:1px 6px;border-radius:4px;font-size:12px;">/v1/</code>.</p>
      </div>
    </div>

    <!-- Authentication -->
    <div class="card mb-24" id="auth">
      <div class="card-header"><h2>Authentication</h2></div>
      <div class="card-body">
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">
          Every API request (except <code style="background:var(--surface2);padding:1px 5px;border-radius:3px;font-size:11px;">/v1/health</code>) must include your API key in the <strong style="color:var(--text);">X-DelkaAI-Key</strong> header.
        </p>
        <div class="code-block">
<span class="c-method">curl</span> https://delka.onrender.com/v1/health \
  -H <span class="c-str">"X-DelkaAI-Key: pk_live_xxxxxxxxxxxxxxxxxxxx..."</span>
        </div>
        <div class="alert alert-warning mt-16">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <span>Never expose your secret key (SK) in client-side code. Use public keys (PK) for browser requests.</span>
        </div>
      </div>
    </div>

    <!-- CV Generation -->
    <div class="card mb-24" id="cv">
      <div class="card-header"><h2>CV Generation</h2></div>
      <div class="card-body">
        <div class="endpoint-block">
          <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <code style="font-size:13px;color:var(--accent-light);">/v1/cv/generate</code>
          </div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Generate a professional, ATS-optimised CV from structured input data.</p>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Request Body</p>
          <div class="code-block">{
  <span class="c-key">"name"</span>:       <span class="c-str">"Jane Smith"</span>,
  <span class="c-key">"email"</span>:      <span class="c-str">"jane@example.com"</span>,
  <span class="c-key">"phone"</span>:      <span class="c-str">"+44 7700 900000"</span>,
  <span class="c-key">"experience"</span>: <span class="c-str">"5 years as Software Engineer at Acme Corp..."</span>,
  <span class="c-key">"skills"</span>:     <span class="c-str">"Python, FastAPI, PostgreSQL, React"</span>,
  <span class="c-key">"education"</span>:  <span class="c-str">"BSc Computer Science, University of Ghana, 2019"</span>
}</div>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin:16px 0 8px;">Example Response</p>
          <div class="code-block">{
  <span class="c-key">"status"</span>: <span class="c-str">"success"</span>,
  <span class="c-key">"data"</span>: {
    <span class="c-key">"cv_text"</span>: <span class="c-str">"Jane Smith\njane@example.com | +44 7700 900000\n\n..."</span>,
    <span class="c-key">"format"</span>: <span class="c-str">"text"</span>
  }
}</div>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin:16px 0 8px;">cURL Example</p>
          <div class="code-block">
<span class="c-method">curl</span> -X POST https://delka.onrender.com/v1/cv/generate \
  -H <span class="c-str">"X-DelkaAI-Key: YOUR_KEY"</span> \
  -H <span class="c-str">"Content-Type: application/json"</span> \
  -d <span class="c-str">'{"name":"Jane Smith","email":"jane@example.com","experience":"...","skills":"..."}'</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Cover Letter -->
    <div class="card mb-24" id="cover-letter">
      <div class="card-header"><h2>Cover Letter</h2></div>
      <div class="card-body">
        <div class="endpoint-block">
          <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <code style="font-size:13px;color:var(--accent-light);">/v1/cover-letter/generate</code>
          </div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Generate a tailored cover letter for a specific job application.</p>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Request Body</p>
          <div class="code-block">{
  <span class="c-key">"name"</span>:         <span class="c-str">"Jane Smith"</span>,
  <span class="c-key">"job_title"</span>:    <span class="c-str">"Software Engineer"</span>,
  <span class="c-key">"company_name"</span>: <span class="c-str">"Tech Solutions Ltd"</span>,
  <span class="c-key">"experience"</span>:   <span class="c-str">"5 years building scalable APIs..."</span>,
  <span class="c-key">"skills"</span>:       <span class="c-str">"Python, FastAPI, PostgreSQL"</span>
}</div>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin:16px 0 8px;">cURL Example</p>
          <div class="code-block">
<span class="c-method">curl</span> -X POST https://delka.onrender.com/v1/cover-letter/generate \
  -H <span class="c-str">"X-DelkaAI-Key: YOUR_KEY"</span> \
  -H <span class="c-str">"Content-Type: application/json"</span> \
  -d <span class="c-str">'{"name":"Jane Smith","job_title":"Engineer","company_name":"Acme"}'</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Support Chat -->
    <div class="card mb-24" id="chat">
      <div class="card-header"><h2>Support Chat</h2></div>
      <div class="card-body">
        <div class="endpoint-block">
          <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <code style="font-size:13px;color:var(--accent-light);">/v1/support/chat</code>
          </div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Send a conversational message and receive an AI-powered response for career or HR support.</p>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Request Body</p>
          <div class="code-block">{
  <span class="c-key">"message"</span>: <span class="c-str">"How do I negotiate a salary offer?"</span>
}</div>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin:16px 0 8px;">cURL Example</p>
          <div class="code-block">
<span class="c-method">curl</span> -X POST https://delka.onrender.com/v1/support/chat \
  -H <span class="c-str">"X-DelkaAI-Key: YOUR_KEY"</span> \
  -H <span class="c-str">"Content-Type: application/json"</span> \
  -d <span class="c-str">'{"message":"How do I negotiate a salary offer?"}'</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Visual Search -->
    <div class="card mb-24" id="vision">
      <div class="card-header"><h2>Visual Search</h2></div>
      <div class="card-body">
        <div class="endpoint-block">
          <div class="endpoint-header">
            <span class="method-badge method-post">POST</span>
            <code style="font-size:13px;color:var(--accent-light);">/v1/vision/analyse</code>
          </div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Upload an image (base64) and extract structured insights using AI vision analysis.</p>

          <p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Request Body</p>
          <div class="code-block">{
  <span class="c-key">"image_base64"</span>: <span class="c-str">"data:image/jpeg;base64,/9j/4AAQ..."</span>,
  <span class="c-key">"prompt"</span>:        <span class="c-str">"Extract all text and data from this document."</span>
}</div>
        </div>
      </div>
    </div>

    <!-- Health Check -->
    <div class="card mb-24" id="health">
      <div class="card-header"><h2>Health Check</h2></div>
      <div class="card-body">
        <div class="endpoint-block">
          <div class="endpoint-header">
            <span class="method-badge method-get">GET</span>
            <code style="font-size:13px;color:var(--accent-light);">/v1/health</code>
          </div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Check API status. Does not require authentication.</p>
          <div class="code-block">
<span class="c-method">curl</span> https://delka.onrender.com/v1/health
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
</body>
</html>
