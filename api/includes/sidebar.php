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
    <!-- DelkaAI animated logo -->
    <svg class="delka-logo-svg" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:28px;height:28px">
      <defs>
        <filter id="sl-ng" x="-150%" y="-150%" width="400%" height="400%">
          <feGaussianBlur stdDeviation="5" result="b1"/>
          <feGaussianBlur stdDeviation="11" result="b2"/>
          <feMerge><feMergeNode in="b2"/><feMergeNode in="b1"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <filter id="sl-sg" x="-100%" y="-100%" width="300%" height="300%">
          <feGaussianBlur stdDeviation="3" result="b"/>
          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <filter id="sl-og" x="-200%" y="-200%" width="500%" height="500%">
          <feGaussianBlur stdDeviation="4" result="b"/>
          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <radialGradient id="sl-nd" cx="38%" cy="32%" r="62%">
          <stop offset="0%"   stop-color="#D0CAFF"/>
          <stop offset="50%"  stop-color="#7C6FFF"/>
          <stop offset="100%" stop-color="#4A3FCC"/>
        </radialGradient>
        <radialGradient id="sl-hz" cx="50%" cy="50%" r="50%">
          <stop offset="0%"   stop-color="#7C6FFF" stop-opacity="1"/>
          <stop offset="100%" stop-color="#7C6FFF" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="sl-ar" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%"   stop-color="#A89BFF" stop-opacity="0"/>
          <stop offset="45%"  stop-color="#7C6FFF" stop-opacity="1"/>
          <stop offset="100%" stop-color="#D0CAFF" stop-opacity=".5"/>
        </linearGradient>
        <linearGradient id="sl-sp" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%"   stop-color="#A89BFF" stop-opacity="0"/>
          <stop offset="40%"  stop-color="#7C6FFF" stop-opacity="1"/>
          <stop offset="100%" stop-color="#D0CAFF" stop-opacity=".5"/>
        </linearGradient>
      </defs>
      <!-- Base D ghost -->
      <line x1="30" y1="12" x2="30" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>
      <line x1="30" y1="12" x2="66"  y2="12"  stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>
      <line x1="30" y1="108" x2="66" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>
      <line x1="30" y1="60" x2="52"  y2="60"  stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>
      <path d="M 66 12 C 124 12 124 108 66 108" stroke="white" stroke-width="7.5" stroke-linecap="round" fill="none" opacity="0.12"/>
      <!-- Shimmer sweep -->
      <line x1="30" y1="12" x2="30" y2="108" stroke="url(#sl-sp)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-spine"     filter="url(#sl-sg)"/>
      <line x1="30" y1="60" x2="52" y2="60"  stroke="url(#sl-ar)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-connector" filter="url(#sl-sg)"/>
      <path d="M 66 12 C 124 12 124 108 66 108" stroke="url(#sl-ar)" stroke-width="7.5" stroke-linecap="round" fill="none" class="delka-anim-arc" filter="url(#sl-sg)"/>
      <!-- Orbit track -->
      <circle cx="60" cy="60" r="24" stroke="#7C6FFF" stroke-width="0.6" fill="none" opacity="0.07"/>
      <!-- Orbiting dot -->
      <g class="delka-anim-orbit" filter="url(#sl-og)">
        <circle cx="60" cy="60" r="4.5" fill="#A89BFF"/>
        <circle cx="60" cy="60" r="2"   fill="white"/>
      </g>
      <!-- Node hazes -->
      <circle cx="60" cy="60" r="20" fill="url(#sl-hz)" class="delka-anim-haze1"/>
      <circle cx="60" cy="60" r="13" fill="url(#sl-hz)" class="delka-anim-haze2"/>
      <!-- Node core -->
      <g class="delka-anim-breathe" filter="url(#sl-ng)">
        <circle cx="60" cy="60" r="9" fill="url(#sl-nd)"/>
        <circle cx="56" cy="56" r="3" fill="white" opacity="0.4"/>
      </g>
    </svg>
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

<!-- Support chat button uses the logo SVG as icon -->
<button id="support-chat-btn" title="Support Chat" aria-label="Open support chat">
  <svg class="delka-logo-svg" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:32px;height:32px">
    <defs>
      <filter id="sb-ng" x="-150%" y="-150%" width="400%" height="400%">
        <feGaussianBlur stdDeviation="5" result="b1"/>
        <feGaussianBlur stdDeviation="11" result="b2"/>
        <feMerge><feMergeNode in="b2"/><feMergeNode in="b1"/><feMergeNode in="SourceGraphic"/></feMerge>
      </filter>
      <filter id="sb-sg" x="-100%" y="-100%" width="300%" height="300%">
        <feGaussianBlur stdDeviation="3" result="b"/>
        <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
      </filter>
      <filter id="sb-og" x="-200%" y="-200%" width="500%" height="500%">
        <feGaussianBlur stdDeviation="4" result="b"/>
        <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
      </filter>
      <radialGradient id="sb-nd" cx="38%" cy="32%" r="62%">
        <stop offset="0%"   stop-color="#D0CAFF"/>
        <stop offset="50%"  stop-color="#fff"/>
        <stop offset="100%" stop-color="#ccc"/>
      </radialGradient>
      <radialGradient id="sb-hz" cx="50%" cy="50%" r="50%">
        <stop offset="0%"   stop-color="#fff" stop-opacity=".6"/>
        <stop offset="100%" stop-color="#fff" stop-opacity="0"/>
      </radialGradient>
      <linearGradient id="sb-ar" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%"   stop-color="#fff" stop-opacity="0"/>
        <stop offset="45%"  stop-color="#fff" stop-opacity=".8"/>
        <stop offset="100%" stop-color="#fff" stop-opacity=".4"/>
      </linearGradient>
      <linearGradient id="sb-sp" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%"   stop-color="#fff" stop-opacity="0"/>
        <stop offset="40%"  stop-color="#fff" stop-opacity=".8"/>
        <stop offset="100%" stop-color="#fff" stop-opacity=".4"/>
      </linearGradient>
    </defs>
    <line x1="30" y1="12" x2="30" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.25"/>
    <line x1="30" y1="12" x2="66"  y2="12"  stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.25"/>
    <line x1="30" y1="108" x2="66" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.25"/>
    <line x1="30" y1="60" x2="52"  y2="60"  stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.25"/>
    <path d="M 66 12 C 124 12 124 108 66 108" stroke="white" stroke-width="7.5" stroke-linecap="round" fill="none" opacity="0.25"/>
    <line x1="30" y1="12" x2="30" y2="108" stroke="url(#sb-sp)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-spine"     filter="url(#sb-sg)"/>
    <line x1="30" y1="60" x2="52" y2="60"  stroke="url(#sb-ar)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-connector" filter="url(#sb-sg)"/>
    <path d="M 66 12 C 124 12 124 108 66 108" stroke="url(#sb-ar)" stroke-width="7.5" stroke-linecap="round" fill="none" class="delka-anim-arc" filter="url(#sb-sg)"/>
    <circle cx="60" cy="60" r="24" stroke="#fff" stroke-width="0.6" fill="none" opacity="0.1"/>
    <g class="delka-anim-orbit" filter="url(#sb-og)">
      <circle cx="60" cy="60" r="4.5" fill="rgba(255,255,255,.8)"/>
      <circle cx="60" cy="60" r="2"   fill="white"/>
    </g>
    <circle cx="60" cy="60" r="20" fill="url(#sb-hz)" class="delka-anim-haze1"/>
    <circle cx="60" cy="60" r="13" fill="url(#sb-hz)" class="delka-anim-haze2"/>
    <g class="delka-anim-breathe" filter="url(#sb-ng)">
      <circle cx="60" cy="60" r="9" fill="url(#sb-nd)"/>
      <circle cx="56" cy="56" r="3" fill="white" opacity="0.5"/>
    </g>
  </svg>
</button>

<div id="support-chat-panel" role="dialog" aria-label="Support chat">
  <div class="sc-panel-head">
    <span>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent,#7c3aed)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      DelkaAI Support
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
