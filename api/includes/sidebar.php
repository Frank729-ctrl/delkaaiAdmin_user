<?php
/**
 * Developer sidebar partial.
 * Expects $active_page string to be set before including.
 */
$active_page = $active_page ?? '';
?>

<!-- Mobile sidebar toggle -->
<button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle navigation">
  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<aside class="sidebar" id="sidebar">
  <a href="/dashboard" class="sidebar-logo">
    <img src="/images/logo.svg" width="28" height="28" alt="DelkaAI">
    <span class="sidebar-logo-text">DelkaAI</span>
  </a>

  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Console</div>

    <a href="/dashboard" class="nav-link <?= $active_page === 'overview' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Overview
    </a>

    <a href="/keys" class="nav-link <?= $active_page === 'keys' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
      API Keys
    </a>

    <a href="/usage" class="nav-link <?= $active_page === 'usage' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Usage
    </a>

    <div class="sidebar-section-label">Developers</div>

    <a href="/docs" class="nav-link <?= $active_page === 'docs' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Documentation
    </a>

    <a href="/playground" class="nav-link <?= $active_page === 'playground' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
      Playground
    </a>

    <a href="/chat" class="nav-link <?= $active_page === 'chat' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      AI Chat
    </a>
  </nav>

  <div class="sidebar-footer">
    <button id="sidebar-support-link" onclick="document.getElementById('support-chat-panel').classList.toggle('sc-open')">
      <img src="/images/logo.svg" width="16" height="16" alt="">
      Delka Support
    </button>
    <a href="/logout">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Sign out
    </a>
  </div>
</aside>

<!-- Floating support chat widget -->
<style>
#support-chat-btn {
  position:fixed; bottom:24px; right:24px; z-index:900;
  width:52px; height:52px; border-radius:50%;
  background:var(--accent,#7c3aed); color:#fff;
  border:none; cursor:pointer; box-shadow:0 4px 20px rgba(124,58,237,.45);
  display:flex; align-items:center; justify-content:center;
  transition:transform .15s, box-shadow .15s;
  overflow: hidden;
}
#support-chat-btn:hover { transform:scale(1.08); box-shadow:0 6px 28px rgba(124,58,237,.55); }
@media (max-width: 768px) {
  #support-chat-btn { display: none; }
  #support-chat-panel { bottom: 0; right: 0; left: 0; width: 100%; border-radius: 14px 14px 0 0; }
}
#sidebar-support-link {
  display: none;
}
@media (max-width: 768px) {
  #sidebar-support-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    color: var(--muted,#6b7280);
    font-size: 13px;
    cursor: pointer;
    transition: color .15s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    border-top: 1px solid var(--border,#2a2d38);
    margin-top: 4px;
  }
  #sidebar-support-link:hover { color: var(--text,#e8eaf0); }
}
#support-chat-panel {
  position:fixed; bottom:86px; right:24px; z-index:901;
  width:340px; max-height:500px;
  background:var(--surface,#18181b); border:1px solid var(--border,#27272a);
  border-radius:14px; box-shadow:0 12px 48px rgba(0,0,0,.45);
  display:none; flex-direction:column; overflow:hidden;
}
#support-chat-panel.sc-open { display:flex; }
.sc-panel-head {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 16px; border-bottom:1px solid var(--border,#27272a);
  font-weight:600; font-size:14px;
}
.sc-panel-head span { display:flex; align-items:center; gap:8px; }
#support-chat-close {
  background:none; border:none; cursor:pointer; color:var(--muted,#71717a);
  padding:2px; line-height:1;
}
#support-chat-messages {
  flex:1; overflow-y:auto; padding:12px 14px;
  display:flex; flex-direction:column; gap:8px; min-height:0;
}
.sc-msg {
  max-width:90%; padding:8px 12px; border-radius:10px; font-size:13px; line-height:1.5;
  word-break:break-word; white-space:pre-wrap;
}
.sc-msg-user      { align-self:flex-end;  background:var(--accent,#7c3aed); color:#fff; border-bottom-right-radius:3px; }
.sc-msg-assistant { align-self:flex-start; background:var(--surface2,#27272a); color:var(--text,#fafafa); border-bottom-left-radius:3px; }
.sc-msg-assistant.sc-thinking { background:transparent; padding:4px 2px; }
.sc-msg-assistant.sc-error    { background:#7f1d1d; color:#fca5a5; }
#support-chat-form {
  display:flex; gap:8px; padding:10px 12px;
  border-top:1px solid var(--border,#27272a);
}
#support-chat-input {
  flex:1; background:var(--surface2,#27272a); border:1px solid var(--border,#27272a);
  border-radius:8px; color:var(--text,#fafafa); font-size:13px; padding:8px 10px;
  resize:none; font-family:inherit;
}
#support-chat-input:focus { outline:none; border-color:var(--accent,#7c3aed); }
#support-chat-form button {
  background:var(--accent,#7c3aed); color:#fff; border:none; border-radius:8px;
  padding:0 14px; cursor:pointer; font-size:13px; font-weight:600;
  transition:background .15s;
}
#support-chat-form button:disabled { opacity:.5; cursor:default; }
</style>

<!-- Support chat button — static logo -->
<button id="support-chat-btn" title="Support Chat" aria-label="Open support chat">
  <img src="/images/logo.svg" width="32" height="32" alt="DelkaAI Support">
</button>

<div id="support-chat-panel" role="dialog" aria-label="Support chat">
  <div class="sc-panel-head">
    <span>
      <img src="/images/logo.svg" width="20" height="20" alt="DelkaAI" style="display:block;">
      Delka Support
    </span>
    <button id="support-chat-close" aria-label="Close chat">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div id="support-chat-messages"></div>
  <form id="support-chat-form" autocomplete="off">
    <textarea id="support-chat-input" rows="1" placeholder="Ask a question..." onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.dispatchEvent(new Event('submit',{cancelable:true,bubbles:true}));}"></textarea>
    <button type="submit">Send</button>
  </form>
</div>
