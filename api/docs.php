<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

$base = rtrim(DELKAI_API_URL, '/');
$active_page = 'docs';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Documentation — DelkaAI</title>
<link rel="stylesheet" href="/css/style.css">
<style>
/* Docs layout */
.docs-layout { display:flex; gap:0; min-height:100vh; }
.docs-sidebar {
  width:220px; flex-shrink:0; border-right:1px solid var(--border);
  padding:24px 0; position:sticky; top:0; height:100vh; overflow-y:auto;
}
.docs-sidebar-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); padding:16px 20px 6px; }
.docs-sidebar a { display:block; font-size:13px; color:var(--muted); padding:6px 20px; text-decoration:none; border-left:2px solid transparent; }
.docs-sidebar a:hover { color:var(--text); background:var(--surface2); }
.docs-sidebar a.active { color:var(--accent); border-left-color:var(--accent); font-weight:600; }
.docs-content { flex:1; max-width:820px; padding:40px 48px; }

/* Section anchors */
.docs-section { margin-bottom:56px; scroll-margin-top:24px; }
.docs-section h2 { font-size:20px; font-weight:700; margin:0 0 8px; padding-bottom:12px; border-bottom:1px solid var(--border); }
.docs-section h3 { font-size:15px; font-weight:600; margin:28px 0 10px; color:var(--text); }
.docs-section p { font-size:14px; color:var(--muted); line-height:1.7; margin:0 0 14px; }

/* Endpoint card */
.endpoint { border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:24px; }
.endpoint-head { display:flex; align-items:center; gap:12px; padding:14px 18px; background:var(--surface2); }
.endpoint-path { font-family:monospace; font-size:13px; font-weight:600; }
.endpoint-desc { font-size:12px; color:var(--muted); margin-left:auto; }
.endpoint-body { padding:18px; }
.endpoint-body p { font-size:13px; color:var(--muted); margin:0 0 14px; line-height:1.6; }

/* Method badges */
.method-post { background:#166534; color:#bbf7d0; font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; }
.method-get  { background:#1e3a5f; color:#bfdbfe; font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; }

/* Param table */
.param-table { width:100%; border-collapse:collapse; font-size:13px; margin:10px 0 0; }
.param-table th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); padding:6px 12px 6px 0; border-bottom:1px solid var(--border); }
.param-table td { padding:8px 12px 8px 0; vertical-align:top; border-bottom:1px solid var(--border); color:var(--text); }
.param-table tr:last-child td { border-bottom:none; }
.param-name { font-family:monospace; font-size:12px; color:var(--accent); }
.param-type { font-family:monospace; font-size:11px; color:var(--muted); }
.param-req  { font-size:10px; background:#7c3aed22; color:var(--accent); border-radius:3px; padding:1px 5px; }
.param-opt  { font-size:10px; background:var(--surface2); color:var(--muted); border-radius:3px; padding:1px 5px; }

/* Code tabs */
.code-tabs { margin:12px 0; }
.code-tab-btns { display:flex; gap:4px; border-bottom:1px solid var(--border); margin-bottom:0; }
.code-tab-btn { font-size:12px; font-weight:500; color:var(--muted); background:none; border:none; border-bottom:2px solid transparent; padding:8px 14px; cursor:pointer; margin-bottom:-1px; }
.code-tab-btn.active { color:var(--text); border-bottom-color:var(--accent); }
.code-tab-pane { display:none; }
.code-tab-pane.active { display:block; }
.code-block-wrap { position:relative; }
.code-block-copy { position:absolute; top:10px; right:10px; font-size:11px; background:var(--surface2); border:1px solid var(--border); color:var(--muted); padding:3px 10px; border-radius:4px; cursor:pointer; }
.code-block-copy:hover { color:var(--text); }
pre.doc-code { background:var(--surface2); border:1px solid var(--border); border-radius:0 0 8px 8px; padding:16px 18px; font-family:monospace; font-size:12.5px; line-height:1.7; overflow-x:auto; margin:0; color:var(--text); }
.hl-key   { color:#86efac; }
.hl-val   { color:#fca5a5; }
.hl-str   { color:#fde68a; }
.hl-cmd   { color:#93c5fd; }
.hl-comm  { color:#6b7280; }

/* Response badge */
.resp-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 7px; border-radius:4px; margin-right:8px; }
.resp-200 { background:#14532d; color:#bbf7d0; }
.resp-4xx { background:#7f1d1d; color:#fca5a5; }

/* Inline code */
code.ic { background:var(--surface2); border:1px solid var(--border); border-radius:4px; padding:1px 6px; font-size:12px; font-family:monospace; }
</style>
</head>
<body>
<div class="layout" style="align-items:stretch;">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <div class="docs-layout" style="flex:1;overflow:hidden;">

    <!-- Docs sidebar -->
    <nav class="docs-sidebar">
      <div class="docs-sidebar-title">Getting Started</div>
      <a href="#quickstart" class="active">Quickstart</a>
      <a href="#authentication">Authentication</a>
      <a href="#errors">Errors</a>

      <div class="docs-sidebar-title">Endpoints</div>
      <a href="#cv">CV Generation</a>
      <a href="#letter">Cover Letter</a>
      <a href="#chat">AI Chat</a>
      <a href="#vision">Visual Search</a>
      <a href="#feedback">Feedback</a>
      <a href="#health">Health Check</a>

      <div class="docs-sidebar-title">Reference</div>
      <a href="#models">Models</a>
      <a href="#rate-limits">Rate Limits</a>
    </nav>

    <!-- Main content -->
    <div class="docs-content">

      <!-- QUICKSTART -->
      <section class="docs-section" id="quickstart">
        <h2>Quickstart</h2>
        <p>Get up and running with the DelkaAI API in minutes. All requests go to the base URL below.</p>

        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">Base URL</span>
          <code class="ic"><?= htmlspecialchars($base) ?></code>
          <button class="copy-btn btn btn-ghost btn-xs" data-copy="<?= htmlspecialchars($base) ?>" style="margin-left:auto;">Copy</button>
        </div>

        <p>Make your first request — no auth needed for the health check:</p>
        <div class="code-tabs">
          <div class="code-tab-btns">
            <button class="code-tab-btn active" data-tab="qs-curl">curl</button>
            <button class="code-tab-btn" data-tab="qs-py">Python</button>
            <button class="code-tab-btn" data-tab="qs-js">JavaScript</button>
          </div>
          <div class="code-tab-pane active" id="qs-curl">
            <div class="code-block-wrap">
              <button class="code-block-copy copy-btn" data-copy="curl <?= htmlspecialchars($base) ?>/v1/health">Copy</button>
<pre class="doc-code"><span class="hl-cmd">curl</span> <?= htmlspecialchars($base) ?>/v1/health</pre>
            </div>
          </div>
          <div class="code-tab-pane" id="qs-py">
            <div class="code-block-wrap">
<pre class="doc-code"><span class="hl-key">import</span> requests

resp = requests.get(<span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/health"</span>)
<span class="hl-key">print</span>(resp.json())</pre>
            </div>
          </div>
          <div class="code-tab-pane" id="qs-js">
            <div class="code-block-wrap">
<pre class="doc-code"><span class="hl-key">const</span> res = <span class="hl-key">await</span> fetch(<span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/health"</span>);
<span class="hl-key">const</span> data = <span class="hl-key">await</span> res.json();
console.log(data);</pre>
            </div>
          </div>
        </div>
      </section>

      <!-- AUTHENTICATION -->
      <section class="docs-section" id="authentication">
        <h2>Authentication</h2>
        <p>Authenticate requests by passing your <strong>Secret Key</strong> in the <code class="ic">X-DelkaAI-Key</code> header. You can create and manage keys on the <a href="/keys" style="color:var(--accent)">API Keys page</a>.</p>

        <div class="code-tabs">
          <div class="code-tab-btns">
            <button class="code-tab-btn active" data-tab="auth-curl">curl</button>
            <button class="code-tab-btn" data-tab="auth-py">Python</button>
            <button class="code-tab-btn" data-tab="auth-js">JavaScript</button>
          </div>
          <div class="code-tab-pane active" id="auth-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> <?= htmlspecialchars($base) ?>/v1/cv/generate \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  -d <span class="hl-str">'{"raw_text": "..."}'</span></pre>
          </div>
          <div class="code-tab-pane" id="auth-py">
<pre class="doc-code">headers = {
    <span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_your_secret_key"</span>,
    <span class="hl-str">"Content-Type"</span>: <span class="hl-str">"application/json"</span>,
}
resp = requests.post(
    <span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/cv/generate"</span>,
    headers=headers,
    json={<span class="hl-str">"raw_text"</span>: <span class="hl-str">"..."</span>},
)</pre>
          </div>
          <div class="code-tab-pane" id="auth-js">
<pre class="doc-code"><span class="hl-key">const</span> res = <span class="hl-key">await</span> fetch(<span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/cv/generate"</span>, {
  method: <span class="hl-str">"POST"</span>,
  headers: {
    <span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_your_secret_key"</span>,
    <span class="hl-str">"Content-Type"</span>: <span class="hl-str">"application/json"</span>,
  },
  body: JSON.stringify({ raw_text: <span class="hl-str">"..."</span> }),
});</pre>
          </div>
        </div>
      </section>

      <!-- ERRORS -->
      <section class="docs-section" id="errors">
        <h2>Errors</h2>
        <p>The API uses standard HTTP status codes. Error responses include a <code class="ic">detail</code> field with a human-readable message.</p>
        <table class="param-table">
          <thead><tr><th>Code</th><th>Meaning</th></tr></thead>
          <tbody>
            <tr><td><span class="param-name">400</span></td><td>Bad request — malformed body or missing required fields.</td></tr>
            <tr><td><span class="param-name">401</span></td><td>Unauthorized — missing or invalid API key.</td></tr>
            <tr><td><span class="param-name">422</span></td><td>Validation error — request body failed schema validation.</td></tr>
            <tr><td><span class="param-name">429</span></td><td>Rate limited — too many requests. Slow down.</td></tr>
            <tr><td><span class="param-name">500</span></td><td>Server error — something went wrong on our end.</td></tr>
          </tbody>
        </table>
      </section>

      <!-- CV GENERATION -->
      <section class="docs-section" id="cv">
        <h2>CV Generation</h2>
        <p>Generate a professionally formatted CV from a raw text description of the applicant's background.</p>

        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-post">POST</span>
            <span class="endpoint-path">/v1/cv/generate</span>
            <span class="endpoint-desc">Generate a CV</span>
          </div>
          <div class="endpoint-body">
            <h3 style="margin-top:0">Request Body</h3>
            <table class="param-table">
              <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
              <tbody>
                <tr>
                  <td><span class="param-name">raw_text</span></td>
                  <td><span class="param-type">string</span></td>
                  <td><span class="param-req">required</span></td>
                  <td>Free-form text describing the applicant's work history, education, and skills.</td>
                </tr>
                <tr>
                  <td><span class="param-name">platform</span></td>
                  <td><span class="param-type">string</span></td>
                  <td><span class="param-opt">optional</span></td>
                  <td>Your platform or app identifier for usage tracking.</td>
                </tr>
              </tbody>
            </table>

            <h3>Example</h3>
            <div class="code-tabs">
              <div class="code-tab-btns">
                <button class="code-tab-btn active" data-tab="cv-curl">curl</button>
                <button class="code-tab-btn" data-tab="cv-py">Python</button>
                <button class="code-tab-btn" data-tab="cv-js">JavaScript</button>
              </div>
              <div class="code-tab-pane active" id="cv-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> -X POST <?= htmlspecialchars($base) ?>/v1/cv/generate \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  -d <span class="hl-str">'{
    "raw_text": "Jane Doe, Software Engineer. 5 years Python, FastAPI, Docker. Previously at TechCorp as Backend Lead. BSc Computer Science, University of Ghana 2018.",
    "platform": "myapp"
  }'</span></pre>
              </div>
              <div class="code-tab-pane" id="cv-py">
<pre class="doc-code">resp = requests.post(
    <span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/cv/generate"</span>,
    headers={<span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>},
    json={
        <span class="hl-str">"raw_text"</span>: <span class="hl-str">"Jane Doe, Software Engineer..."</span>,
        <span class="hl-str">"platform"</span>: <span class="hl-str">"myapp"</span>,
    },
)
cv = resp.json()
<span class="hl-key">print</span>(cv[<span class="hl-str">"full_name"</span>], cv[<span class="hl-str">"experience"</span>])</pre>
              </div>
              <div class="code-tab-pane" id="cv-js">
<pre class="doc-code"><span class="hl-key">const</span> res = <span class="hl-key">await</span> fetch(<span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/cv/generate"</span>, {
  method: <span class="hl-str">"POST"</span>,
  headers: { <span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>,
             <span class="hl-str">"Content-Type"</span>: <span class="hl-str">"application/json"</span> },
  body: JSON.stringify({
    raw_text: <span class="hl-str">"Jane Doe, Software Engineer..."</span>,
    platform: <span class="hl-str">"myapp"</span>,
  }),
});
<span class="hl-key">const</span> cv = <span class="hl-key">await</span> res.json();</pre>
              </div>
            </div>

            <h3>Response</h3>
<pre class="doc-code">{
  <span class="hl-key">"full_name"</span>: <span class="hl-str">"Jane Doe"</span>,
  <span class="hl-key">"email"</span>: <span class="hl-str">"jane@example.com"</span>,
  <span class="hl-key">"phone"</span>: <span class="hl-str">"+1 234 567 8900"</span>,
  <span class="hl-key">"summary"</span>: <span class="hl-str">"Experienced software engineer..."</span>,
  <span class="hl-key">"experience"</span>: [
    {
      <span class="hl-key">"company"</span>: <span class="hl-str">"TechCorp"</span>,
      <span class="hl-key">"title"</span>: <span class="hl-str">"Backend Lead"</span>,
      <span class="hl-key">"start_date"</span>: <span class="hl-str">"2019-01"</span>,
      <span class="hl-key">"end_date"</span>: <span class="hl-str">"Present"</span>,
      <span class="hl-key">"bullets"</span>: [<span class="hl-str">"Led migration..."</span>]
    }
  ],
  <span class="hl-key">"education"</span>: [...],
  <span class="hl-key">"skills"</span>: [<span class="hl-str">"Python"</span>, <span class="hl-str">"FastAPI"</span>, <span class="hl-str">"Docker"</span>]
}</pre>
          </div>
        </div>
      </section>

      <!-- COVER LETTER -->
      <section class="docs-section" id="letter">
        <h2>Cover Letter</h2>
        <p>Generate a tailored, job-specific cover letter based on the applicant's background and the job posting.</p>

        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-post">POST</span>
            <span class="endpoint-path">/v1/cover-letter/generate</span>
            <span class="endpoint-desc">Generate a cover letter</span>
          </div>
          <div class="endpoint-body">
            <h3 style="margin-top:0">Request Body</h3>
            <table class="param-table">
              <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
              <tbody>
                <tr><td><span class="param-name">applicant_name</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Full name of the applicant.</td></tr>
                <tr><td><span class="param-name">company_name</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Name of the company being applied to.</td></tr>
                <tr><td><span class="param-name">job_title</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>The job title / role.</td></tr>
                <tr><td><span class="param-name">job_description</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>The full job description or posting text.</td></tr>
                <tr><td><span class="param-name">applicant_background</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Summary of the applicant's relevant experience and skills.</td></tr>
                <tr><td><span class="param-name">platform</span></td><td><span class="param-type">string</span></td><td><span class="param-opt">optional</span></td><td>Your platform identifier.</td></tr>
              </tbody>
            </table>

            <h3>Example</h3>
            <div class="code-tabs">
              <div class="code-tab-btns">
                <button class="code-tab-btn active" data-tab="cl-curl">curl</button>
                <button class="code-tab-btn" data-tab="cl-py">Python</button>
              </div>
              <div class="code-tab-pane active" id="cl-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> -X POST <?= htmlspecialchars($base) ?>/v1/cover-letter/generate \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  -d <span class="hl-str">'{
    "applicant_name": "Jane Doe",
    "company_name": "Acme Corp",
    "job_title": "Senior Backend Engineer",
    "job_description": "We are looking for...",
    "applicant_background": "5 years Python, FastAPI..."
  }'</span></pre>
              </div>
              <div class="code-tab-pane" id="cl-py">
<pre class="doc-code">resp = requests.post(
    <span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/cover-letter/generate"</span>,
    headers={<span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>},
    json={
        <span class="hl-str">"applicant_name"</span>: <span class="hl-str">"Jane Doe"</span>,
        <span class="hl-str">"company_name"</span>: <span class="hl-str">"Acme Corp"</span>,
        <span class="hl-str">"job_title"</span>: <span class="hl-str">"Senior Backend Engineer"</span>,
        <span class="hl-str">"job_description"</span>: <span class="hl-str">"We are looking for..."</span>,
        <span class="hl-str">"applicant_background"</span>: <span class="hl-str">"5 years Python..."</span>,
    },
)
<span class="hl-key">print</span>(resp.json()[<span class="hl-str">"letter"</span>])</pre>
              </div>
            </div>

            <h3>Response</h3>
<pre class="doc-code">{
  <span class="hl-key">"letter"</span>: <span class="hl-str">"Dear Hiring Manager,\n\nI am writing to express..."</span>
}</pre>
          </div>
        </div>
      </section>

      <!-- CHAT -->
      <section class="docs-section" id="chat">
        <h2>AI Chat</h2>
        <p>Multi-turn conversational AI for career guidance, job-search support, and general queries. Responses are delivered as Server-Sent Events (SSE).</p>

        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-post">POST</span>
            <span class="endpoint-path">/v1/chat</span>
            <span class="endpoint-desc">Streaming AI chat</span>
          </div>
          <div class="endpoint-body">
            <h3 style="margin-top:0">Request Body</h3>
            <table class="param-table">
              <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
              <tbody>
                <tr><td><span class="param-name">message</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>The user's message.</td></tr>
                <tr><td><span class="param-name">user_id</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Unique identifier for the end user.</td></tr>
                <tr><td><span class="param-name">session_id</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Conversation session ID. Reuse the same value to maintain context across turns.</td></tr>
                <tr><td><span class="param-name">platform</span></td><td><span class="param-type">string</span></td><td><span class="param-opt">optional</span></td><td>Your platform identifier for usage tracking.</td></tr>
              </tbody>
            </table>

            <h3>Example</h3>
            <div class="code-tabs">
              <div class="code-tab-btns">
                <button class="code-tab-btn active" data-tab="chat-curl">curl</button>
                <button class="code-tab-btn" data-tab="chat-js">JavaScript (SSE)</button>
              </div>
              <div class="code-tab-pane active" id="chat-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> -X POST <?= htmlspecialchars($base) ?>/v1/chat \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  --no-buffer \
  -d <span class="hl-str">'{
    "message": "How do I write a strong CV summary?",
    "user_id": "user-abc",
    "session_id": "session-123"
  }'</span></pre>
              </div>
              <div class="code-tab-pane" id="chat-js">
<pre class="doc-code"><span class="hl-comm">// Using the Fetch API with streaming</span>
<span class="hl-key">const</span> res = <span class="hl-key">await</span> fetch(<span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/chat"</span>, {
  method: <span class="hl-str">"POST"</span>,
  headers: { <span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>,
             <span class="hl-str">"Content-Type"</span>: <span class="hl-str">"application/json"</span> },
  body: JSON.stringify({
    message: <span class="hl-str">"How do I improve my CV?"</span>,
    user_id: <span class="hl-str">"user-abc"</span>,
    session_id: <span class="hl-str">"session-123"</span>,
  }),
});

<span class="hl-key">const</span> reader = res.body.getReader();
<span class="hl-key">const</span> decoder = <span class="hl-key">new</span> TextDecoder();
<span class="hl-key">while</span> (<span class="hl-key">true</span>) {
  <span class="hl-key">const</span> { done, value } = <span class="hl-key">await</span> reader.read();
  <span class="hl-key">if</span> (done) <span class="hl-key">break</span>;
  process.stdout.write(decoder.decode(value));
}</pre>
              </div>
            </div>

            <p style="margin-top:14px;">The response is a stream of <code class="ic">text/event-stream</code> tokens. Each <code class="ic">data:</code> line contains one text chunk; the stream ends with <code class="ic">data: [DONE]</code>.</p>
          </div>
        </div>
      </section>

      <!-- VISION -->
      <section class="docs-section" id="vision">
        <h2>Visual Search</h2>
        <p>Analyse an image URL and extract structured career-relevant information — job titles, skills, and context described in the image.</p>

        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-post">POST</span>
            <span class="endpoint-path">/v1/vision/search</span>
            <span class="endpoint-desc">Analyse an image</span>
          </div>
          <div class="endpoint-body">
            <h3 style="margin-top:0">Request Body</h3>
            <table class="param-table">
              <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
              <tbody>
                <tr><td><span class="param-name">image_url</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Publicly accessible URL of the image to analyse (JPEG, PNG, WebP).</td></tr>
                <tr><td><span class="param-name">platform</span></td><td><span class="param-type">string</span></td><td><span class="param-opt">optional</span></td><td>Your platform identifier.</td></tr>
              </tbody>
            </table>

            <h3>Example</h3>
            <div class="code-tabs">
              <div class="code-tab-btns">
                <button class="code-tab-btn active" data-tab="vis-curl">curl</button>
                <button class="code-tab-btn" data-tab="vis-py">Python</button>
              </div>
              <div class="code-tab-pane active" id="vis-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> -X POST <?= htmlspecialchars($base) ?>/v1/vision/search \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  -d <span class="hl-str">'{
    "image_url": "https://example.com/resume-scan.jpg",
    "platform": "myapp"
  }'</span></pre>
              </div>
              <div class="code-tab-pane" id="vis-py">
<pre class="doc-code">resp = requests.post(
    <span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/vision/search"</span>,
    headers={<span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>},
    json={<span class="hl-str">"image_url"</span>: <span class="hl-str">"https://example.com/resume-scan.jpg"</span>},
)
<span class="hl-key">print</span>(resp.json())</pre>
              </div>
            </div>

            <h3>Response</h3>
<pre class="doc-code">{
  <span class="hl-key">"description"</span>: <span class="hl-str">"Image shows a resume for a software engineer..."</span>,
  <span class="hl-key">"extracted_text"</span>: <span class="hl-str">"Jane Doe | Software Engineer | Python, FastAPI..."</span>,
  <span class="hl-key">"tags"</span>: [<span class="hl-str">"resume"</span>, <span class="hl-str">"software engineer"</span>, <span class="hl-str">"Python"</span>]
}</pre>
          </div>
        </div>
      </section>

      <!-- FEEDBACK -->
      <section class="docs-section" id="feedback">
        <h2>Feedback</h2>
        <p>Submit a star rating and optional comment for any completed session. Helps improve DelkaAI response quality over time.</p>

        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-post">POST</span>
            <span class="endpoint-path">/v1/feedback</span>
            <span class="endpoint-desc">Submit session feedback</span>
          </div>
          <div class="endpoint-body">
            <h3 style="margin-top:0">Request Body</h3>
            <table class="param-table">
              <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
              <tbody>
                <tr><td><span class="param-name">session_id</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>The session ID returned by any generation or chat request.</td></tr>
                <tr><td><span class="param-name">service</span></td><td><span class="param-type">string</span></td><td><span class="param-req">required</span></td><td>Which service to rate: <code class="ic">cv</code>, <code class="ic">cover_letter</code>, <code class="ic">chat</code>, or <code class="ic">vision</code>.</td></tr>
                <tr><td><span class="param-name">rating</span></td><td><span class="param-type">integer</span></td><td><span class="param-req">required</span></td><td>Score from 1 (poor) to 5 (excellent).</td></tr>
                <tr><td><span class="param-name">comment</span></td><td><span class="param-type">string</span></td><td><span class="param-opt">optional</span></td><td>Free-text feedback.</td></tr>
              </tbody>
            </table>

            <h3>Example</h3>
            <div class="code-tabs">
              <div class="code-tab-btns">
                <button class="code-tab-btn active" data-tab="fb-curl">curl</button>
                <button class="code-tab-btn" data-tab="fb-py">Python</button>
              </div>
              <div class="code-tab-pane active" id="fb-curl">
<pre class="doc-code"><span class="hl-cmd">curl</span> -X POST <?= htmlspecialchars($base) ?>/v1/feedback \
  -H <span class="hl-str">"X-DelkaAI-Key: sk_live_your_secret_key"</span> \
  -H <span class="hl-str">"Content-Type: application/json"</span> \
  -d <span class="hl-str">'{
    "session_id": "session-xyz-456",
    "service": "cv",
    "rating": 5,
    "comment": "Very accurate and professional output."
  }'</span></pre>
              </div>
              <div class="code-tab-pane" id="fb-py">
<pre class="doc-code">resp = requests.post(
    <span class="hl-str">"<?= htmlspecialchars($base) ?>/v1/feedback"</span>,
    headers={<span class="hl-str">"X-DelkaAI-Key"</span>: <span class="hl-str">"sk_live_..."</span>},
    json={
        <span class="hl-str">"session_id"</span>: <span class="hl-str">"session-xyz-456"</span>,
        <span class="hl-str">"service"</span>:    <span class="hl-str">"cv"</span>,
        <span class="hl-str">"rating"</span>:     5,
        <span class="hl-str">"comment"</span>:    <span class="hl-str">"Great output!"</span>,
    },
)
<span class="hl-key">print</span>(resp.json())</pre>
              </div>
            </div>

            <h3>Response</h3>
<pre class="doc-code">{
  <span class="hl-key">"success"</span>: <span class="hl-val">true</span>,
  <span class="hl-key">"feedback_id"</span>: <span class="hl-str">"fb_abc123"</span>
}</pre>
          </div>
        </div>
      </section>

      <!-- HEALTH -->
      <section class="docs-section" id="health">
        <h2>Health Check</h2>
        <p>Check API availability and provider status. No authentication required.</p>
        <div class="endpoint">
          <div class="endpoint-head">
            <span class="method-get">GET</span>
            <span class="endpoint-path">/v1/health</span>
            <span class="endpoint-desc">No auth required</span>
          </div>
          <div class="endpoint-body">
<pre class="doc-code"><span class="hl-cmd">curl</span> <?= htmlspecialchars($base) ?>/v1/health</pre>
            <h3>Response</h3>
<pre class="doc-code">{
  <span class="hl-key">"status"</span>: <span class="hl-str">"ok"</span>,
  <span class="hl-key">"providers"</span>: { <span class="hl-key">"groq"</span>: <span class="hl-str">"available"</span> },
  <span class="hl-key">"version"</span>: <span class="hl-str">"1.0.0"</span>
}</pre>
          </div>
        </div>
      </section>

      <!-- MODELS -->
      <section class="docs-section" id="models">
        <h2>Models</h2>
        <p>DelkaAI uses Groq's LLM inference with automatic fallback to a local model.</p>
        <table class="param-table">
          <thead><tr><th>Task</th><th>Primary Model</th><th>Fallback</th></tr></thead>
          <tbody>
            <tr><td>CV Generation</td><td><code class="ic">llama-3.3-70b-versatile</code></td><td>llama3.1 (Ollama)</td></tr>
            <tr><td>Cover Letter</td><td><code class="ic">llama-3.3-70b-versatile</code></td><td>llama3.1 (Ollama)</td></tr>
            <tr><td>AI Chat</td><td><code class="ic">llama-3.1-8b-instant</code></td><td>mistral (Ollama)</td></tr>
            <tr><td>Visual Search</td><td><code class="ic">llama-3.2-11b-vision-preview</code></td><td>llava (Ollama)</td></tr>
          </tbody>
        </table>
      </section>

      <!-- RATE LIMITS -->
      <section class="docs-section" id="rate-limits">
        <h2>Rate Limits</h2>
        <p>Rate limits are applied per API key and per IP address.</p>
        <table class="param-table">
          <thead><tr><th>Limit</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td>Requests per minute (per key)</td><td>30</td></tr>
            <tr><td>Requests per minute (per IP)</td><td>60</td></tr>
            <tr><td>Burst (per second)</td><td>5</td></tr>
          </tbody>
        </table>
        <p>When rate limited, the API returns <code class="ic">429 Too Many Requests</code>. Implement exponential backoff in your client.</p>
      </section>

    </div>
  </div>
</div>

<script src="/js/app.js"></script>
<script>
// Code tabs
document.querySelectorAll('.code-tab-btns').forEach(function(btnGroup) {
  btnGroup.querySelectorAll('.code-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var tabId = btn.getAttribute('data-tab');
      // Deactivate all in this group
      btnGroup.querySelectorAll('.code-tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      // Find parent .code-tabs and toggle panes
      var tabs = btnGroup.closest('.code-tabs');
      tabs.querySelectorAll('.code-tab-pane').forEach(function(pane) {
        pane.classList.toggle('active', pane.id === tabId);
      });
    });
  });
});

// Docs sidebar active tracking
var sections = document.querySelectorAll('.docs-section');
var links = document.querySelectorAll('.docs-sidebar a');
window.addEventListener('scroll', function() {
  var pos = window.scrollY + 80;
  sections.forEach(function(sec) {
    if (pos >= sec.offsetTop) {
      links.forEach(l => l.classList.remove('active'));
      var link = document.querySelector('.docs-sidebar a[href="#' + sec.id + '"]');
      if (link) link.classList.add('active');
    }
  });
}, { passive: true });
</script>
</body>
</html>
