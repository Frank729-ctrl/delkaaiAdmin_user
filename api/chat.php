<?php
/**
 * DelkaAI Chat — Claude-style layout with conversation history.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

$active_page = 'chat';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
/* ── Full-page flex layout ───────────────────────────────────── */
.chat-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
  flex: 1;
}

/* ── Conversation history panel ──────────────────────────────── */
.conv-panel {
  width: 240px;
  flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: width .2s ease, opacity .2s ease;
}
.conv-panel.cp-hidden {
  width: 0;
  opacity: 0;
  border-right: none;
  pointer-events: none;
}

.conv-panel-top {
  padding: 14px 12px 10px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.conv-model-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--accent);
  letter-spacing: .03em;
  white-space: nowrap;
}
.conv-model-badge svg { flex-shrink: 0; }

.new-chat-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 7px;
  border: 1px solid var(--border);
  background: none;
  color: var(--muted);
  cursor: pointer;
  flex-shrink: 0;
  transition: color .15s, border-color .15s, background .15s;
}
.new-chat-btn:hover { color: var(--text); border-color: var(--muted); background: var(--surface2); }

.conv-list {
  flex: 1;
  overflow-y: auto;
  padding: 4px 0 12px;
}
.conv-list::-webkit-scrollbar { width: 3px; }
.conv-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.conv-group-label {
  font-size: 10px;
  font-weight: 700;
  color: var(--muted);
  letter-spacing: .06em;
  text-transform: uppercase;
  padding: 10px 14px 4px;
}

.conv-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 12px;
  cursor: pointer;
  border-radius: 7px;
  margin: 1px 6px;
  font-size: 12px;
  color: var(--muted);
  line-height: 1.4;
  transition: background .12s, color .12s;
  white-space: nowrap;
  overflow: hidden;
  position: relative;
}
.conv-item:hover { background: var(--surface2); color: var(--text); }
.conv-item.active { background: var(--surface2); color: var(--text); }
.conv-item.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 20%;
  height: 60%;
  width: 2.5px;
  background: var(--accent);
  border-radius: 0 2px 2px 0;
}
.conv-item-title {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}
.conv-item-del {
  opacity: 0;
  background: none;
  border: none;
  color: var(--muted);
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 4px;
  font-size: 13px;
  line-height: 1;
  flex-shrink: 0;
  transition: opacity .12s, color .12s;
}
.conv-item:hover .conv-item-del { opacity: 1; }
.conv-item-del:hover { color: #f87171; }

/* ── Main chat area ───────────────────────────────────────────── */
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

/* Topbar */
.chat-topbar {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 20px;
  height: 52px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.chat-topbar-toggle {
  background: none;
  border: none;
  color: var(--muted);
  cursor: pointer;
  padding: 6px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  transition: color .15s, background .15s;
}
.chat-topbar-toggle:hover { color: var(--text); background: var(--surface2); }
.chat-topbar-title {
  flex: 1;
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.chat-topbar-model {
  font-size: 11px;
  color: var(--muted);
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 3px 10px;
  white-space: nowrap;
}

/* Messages */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 32px 0 8px;
  display: flex;
  flex-direction: column;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* Empty state */
.chat-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  padding: 40px 24px;
  text-align: center;
}
.chat-empty-logo { opacity: .7; }
.chat-empty h2 { font-size: 22px; font-weight: 700; margin: 0; color: var(--text); }
.chat-empty p { font-size: 13px; color: var(--muted); margin: 0; max-width: 360px; line-height: 1.65; }
.chat-suggestions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-top: 4px;
  max-width: 520px;
}
.chat-suggestion {
  font-size: 12px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 8px 14px;
  cursor: pointer;
  color: var(--text);
  text-align: left;
  line-height: 1.4;
  transition: border-color .15s, background .15s;
}
.chat-suggestion:hover { border-color: var(--accent); background: var(--surface); }

/* Message rows */
.chat-row {
  display: flex;
  flex-direction: column;
  padding: 4px 0;
}
.chat-row-inner {
  display: flex;
  gap: 12px;
  padding: 6px 24px;
  align-items: flex-start;
  max-width: 860px;
  width: 100%;
  margin: 0 auto;
  box-sizing: border-box;
}
.chat-row-user .chat-row-inner { flex-direction: row-reverse; }

/* Avatar */
.chat-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  margin-top: 2px;
}
.chat-avatar-delka {
  background: var(--accent);
  color: #fff;
}
.chat-avatar-user {
  background: var(--surface2);
  color: var(--text);
  border: 1px solid var(--border);
  font-size: 10px;
}

/* Bubble body */
.chat-bubble-wrap { min-width: 0; flex: 1; }
.chat-sender {
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  margin-bottom: 4px;
  letter-spacing: .02em;
}
.chat-row-user .chat-sender { text-align: right; }

.chat-bubble {
  font-size: 13.5px;
  line-height: 1.7;
  word-break: break-word;
  color: var(--text);
}
/* AI bubble — flat, no background */
.chat-bubble-delka {
  background: transparent;
  padding: 0;
}
/* User bubble — subtle pill */
.chat-bubble-user {
  display: inline-block;
  background: var(--accent);
  color: #fff;
  padding: 9px 14px;
  border-radius: 16px 16px 4px 16px;
  max-width: 520px;
  float: right;
  clear: both;
}
.chat-row-user .chat-bubble-wrap { display: flex; flex-direction: column; align-items: flex-end; }

/* Thinking bubble */
.chat-bubble-thinking { background: transparent; padding: 4px 0; }

/* Meta */
.chat-meta {
  font-size: 10px;
  color: var(--muted);
  margin-top: 5px;
  opacity: .7;
}
.chat-row-user .chat-meta { text-align: right; }

/* Markdown inside Delka bubbles */
.chat-bubble-delka p       { margin: 0 0 8px; }
.chat-bubble-delka p:last-child { margin-bottom: 0; }
.chat-bubble-delka h1,
.chat-bubble-delka h2,
.chat-bubble-delka h3      { font-size: 14px; font-weight: 700; margin: 14px 0 5px; }
.chat-bubble-delka ul,
.chat-bubble-delka ol      { margin: 6px 0 8px; padding-left: 22px; }
.chat-bubble-delka li      { margin-bottom: 4px; }
.chat-bubble-delka code    { background: var(--surface2); border: 1px solid var(--border); border-radius: 4px; padding: 1px 6px; font-family: monospace; font-size: 12px; }
.chat-bubble-delka pre     { background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 12px 14px; overflow-x: auto; margin: 10px 0; }
.chat-bubble-delka pre code { background: none; border: none; padding: 0; }
.chat-bubble-delka strong  { font-weight: 700; }
.chat-bubble-delka em      { font-style: italic; }
.chat-bubble-delka blockquote { border-left: 3px solid var(--accent); margin: 8px 0; padding: 4px 0 4px 12px; color: var(--muted); }
.chat-bubble-delka hr      { border: none; border-top: 1px solid var(--border); margin: 12px 0; }
.chat-bubble-delka table   { border-collapse: collapse; width: 100%; margin: 8px 0; font-size: 12px; }
.chat-bubble-delka th,
.chat-bubble-delka td      { border: 1px solid var(--border); padding: 6px 10px; text-align: left; }
.chat-bubble-delka th      { background: var(--surface2); font-weight: 700; }

/* Error bubble */
.chat-bubble-error { color: #f87171 !important; }

/* ── Input bar ───────────────────────────────────────────────── */
.chat-input-area {
  flex-shrink: 0;
  padding: 8px 24px 20px;
}
.chat-input-outer {
  max-width: 812px;
  margin: 0 auto;
}
.chat-input-box {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 12px 14px 10px;
  transition: border-color .15s, box-shadow .15s;
}
.chat-input-box:focus-within {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,58,237,.12);
}
#chat-input {
  width: 100%;
  background: none;
  border: none;
  color: var(--text);
  font-size: 13.5px;
  font-family: inherit;
  resize: none;
  line-height: 1.55;
  max-height: 140px;
  overflow-y: auto;
  outline: none;
  box-sizing: border-box;
}
#chat-input::placeholder { color: var(--muted); }

.chat-input-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
}
.chat-input-model {
  font-size: 10.5px;
  color: var(--muted);
  display: flex;
  align-items: center;
  gap: 5px;
}
.chat-input-model-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent);
  opacity: .7;
}
.chat-send-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--accent);
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .15s, transform .1s, opacity .15s;
}
.chat-send-btn:hover:not(:disabled) { background: #6d28d9; transform: scale(1.05); }
.chat-send-btn:disabled { opacity: .35; cursor: default; transform: none; }

.chat-hint {
  font-size: 10.5px;
  color: var(--muted);
  text-align: center;
  margin-top: 7px;
}

/* ── Mobile adjustments ──────────────────────────────────────── */
@media (max-width: 768px) {
  .conv-panel { position: fixed; top: 0; left: 0; height: 100vh; z-index: 200; transform: translateX(-100%); transition: transform .2s ease; }
  .conv-panel.cp-mobile-open { transform: translateX(0); }
  .conv-panel.cp-hidden { display: none; }
  .chat-row-inner { padding: 6px 14px; }
  .chat-bubble-user { max-width: 85%; }
  .chat-input-area { padding: 8px 14px 16px; }
}
</style>
</head>
<body>
<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content" style="padding:0;overflow:hidden;display:flex;flex:1;">
    <div class="chat-shell">

      <!-- ── Conversation history panel ── -->
      <div class="conv-panel" id="conv-panel">
        <div class="conv-panel-top">
          <div class="conv-model-badge">
            <img src="/images/logo.svg" width="16" height="16" alt="Delka">
            Delka Spark 1.0
          </div>
          <button class="new-chat-btn" id="new-chat-btn" title="New chat">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>
        </div>
        <div class="conv-list" id="conv-list">
          <!-- populated by JS -->
        </div>
      </div>

      <!-- ── Main chat ── -->
      <div class="chat-main">

        <!-- Topbar -->
        <div class="chat-topbar">
          <button class="chat-topbar-toggle" id="history-toggle" title="Toggle history">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="chat-topbar-title" id="chat-topbar-title">New Chat</span>
          <span class="chat-topbar-model">Delka Spark 1.0</span>
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chat-messages"></div>

        <!-- Input -->
        <div class="chat-input-area">
          <div class="chat-input-outer">
            <div class="chat-input-box">
              <textarea id="chat-input" rows="1" placeholder="Message Delka…" aria-label="Chat message"></textarea>
              <div class="chat-input-footer">
                <span class="chat-input-model">
                  <span class="chat-input-model-dot"></span>
                  Delka Spark 1.0
                </span>
                <button type="button" class="chat-send-btn" id="chat-send-btn" aria-label="Send">
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                </button>
              </div>
            </div>
            <p class="chat-hint">Enter to send &nbsp;·&nbsp; Shift+Enter for new line</p>
          </div>
        </div>

      </div><!-- /chat-main -->
    </div><!-- /chat-shell -->
  </main>
</div>

<script src="/js/app.js"></script>
<script>
(function () {
  'use strict';

  // ── Constants ──────────────────────────────────────────────────
  var STORE_KEY    = 'delkai_conversations_v2';
  var SESSION_KEY  = 'delkai_chat_session';
  var MODEL_NAME   = 'Delka Spark 1.0';
  var SUGGESTIONS  = [
    'What is the current dollar to cedi exchange rate?',
    'Write a Python function to validate a phone number',
    'Help me draft a professional email declining a meeting',
    'Explain how blockchain works in simple terms',
    'What are the top companies hiring software engineers in Ghana?',
  ];

  // ── DOM refs ───────────────────────────────────────────────────
  var messagesEl    = document.getElementById('chat-messages');
  var inputEl       = document.getElementById('chat-input');
  var sendBtn       = document.getElementById('chat-send-btn');
  var convPanel     = document.getElementById('conv-panel');
  var convList      = document.getElementById('conv-list');
  var newChatBtn    = document.getElementById('new-chat-btn');
  var historyToggle = document.getElementById('history-toggle');
  var topbarTitle   = document.getElementById('chat-topbar-title');

  // ── Marked config ──────────────────────────────────────────────
  marked.setOptions({ breaks: true, gfm: true });

  // ── Conversation store ─────────────────────────────────────────
  // { conversations: {[id]: {id, title, messages, createdAt, updatedAt}}, activeId }
  function loadStore() {
    try { return JSON.parse(localStorage.getItem(STORE_KEY) || 'null') || { conversations: {}, activeId: null }; }
    catch(e) { return { conversations: {}, activeId: null }; }
  }
  function saveStore(s) { localStorage.setItem(STORE_KEY, JSON.stringify(s)); }

  function newConvId() { return 'c-' + Date.now() + '-' + Math.random().toString(36).slice(2,6); }

  function getConv(store, id) { return store.conversations[id] || null; }

  function createConv(store) {
    var id = newConvId();
    store.conversations[id] = { id: id, title: 'New Chat', messages: [], createdAt: Date.now(), updatedAt: Date.now() };
    store.activeId = id;
    return id;
  }

  function setTitle(store, id, text) {
    if (!store.conversations[id]) return;
    var t = text.trim().slice(0, 48);
    store.conversations[id].title = t || 'New Chat';
  }

  // ── Session (backend) ──────────────────────────────────────────
  var backendSession = localStorage.getItem(SESSION_KEY) || ('console-' + Math.random().toString(36).slice(2));
  function saveBackendSession(id) { backendSession = id; localStorage.setItem(SESSION_KEY, id); }

  // ── History panel render ───────────────────────────────────────
  function formatGroupLabel(ts) {
    var now  = new Date();
    var d    = new Date(ts);
    var diff = Math.floor((now - d) / 86400000);
    if (diff === 0) return 'Today';
    if (diff === 1) return 'Yesterday';
    if (diff <= 6) return 'Last 7 days';
    if (diff <= 29) return 'Last 30 days';
    return d.toLocaleString('default', { month: 'long', year: 'numeric' });
  }

  function renderHistory(store) {
    var convs = Object.values(store.conversations).sort(function(a,b){ return b.updatedAt - a.updatedAt; });
    if (convs.length === 0) { convList.innerHTML = ''; return; }

    var groups = {};
    convs.forEach(function(c) {
      var g = formatGroupLabel(c.updatedAt);
      if (!groups[g]) groups[g] = [];
      groups[g].push(c);
    });

    var html = '';
    Object.keys(groups).forEach(function(g) {
      html += '<div class="conv-group-label">' + escHtml(g) + '</div>';
      groups[g].forEach(function(c) {
        var active = c.id === store.activeId ? ' active' : '';
        html += '<div class="conv-item' + active + '" data-id="' + c.id + '">'
          + '<span class="conv-item-title">' + escHtml(c.title) + '</span>'
          + '<button class="conv-item-del" data-del="' + c.id + '" title="Delete">×</button>'
          + '</div>';
      });
    });
    convList.innerHTML = html;

    convList.querySelectorAll('.conv-item').forEach(function(el) {
      el.addEventListener('click', function(e) {
        if (e.target.closest('.conv-item-del')) return;
        switchConv(el.getAttribute('data-id'));
      });
    });
    convList.querySelectorAll('.conv-item-del').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        deleteConv(btn.getAttribute('data-del'));
      });
    });
  }

  // ── Switch / delete conversation ───────────────────────────────
  function switchConv(id) {
    var store = loadStore();
    if (!store.conversations[id]) return;
    store.activeId = id;
    saveStore(store);
    renderHistory(store);
    renderMessages(store.conversations[id]);
    topbarTitle.textContent = store.conversations[id].title;
    if (window.innerWidth <= 768) convPanel.classList.remove('cp-mobile-open');
  }

  function deleteConv(id) {
    var store = loadStore();
    delete store.conversations[id];
    if (store.activeId === id) {
      var remaining = Object.keys(store.conversations);
      store.activeId = remaining.length > 0 ? remaining[0] : null;
    }
    saveStore(store);
    if (store.activeId) {
      renderHistory(store);
      renderMessages(store.conversations[store.activeId]);
      topbarTitle.textContent = store.conversations[store.activeId].title;
    } else {
      renderHistory(store);
      showEmpty();
      topbarTitle.textContent = 'New Chat';
    }
  }

  // ── Render messages for a conversation ────────────────────────
  function renderMessages(conv) {
    messagesEl.innerHTML = '';
    if (!conv || conv.messages.length === 0) { showEmpty(); return; }
    conv.messages.forEach(function(m) { appendBubble(m.role, m.text, m.ts, false, true); });
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  // ── Empty state ────────────────────────────────────────────────
  function showEmpty() {
    messagesEl.innerHTML =
      '<div class="chat-empty">' +
        '<div class="chat-empty-logo">' + delkaSpinnerSvg('em') + '</div>' +
        '<h2>How can Delka help?</h2>' +
        '<p>General-purpose AI assistant. Ask about code, careers, writing, business, Ghana — anything.</p>' +
        '<div class="chat-suggestions">' +
          SUGGESTIONS.map(function(s) {
            return '<button class="chat-suggestion" type="button">' + escHtml(s) + '</button>';
          }).join('') +
        '</div>' +
      '</div>';
    messagesEl.querySelectorAll('.chat-suggestion').forEach(function(btn) {
      btn.addEventListener('click', function() {
        inputEl.value = btn.textContent;
        autoResize();
        sendMessage();
      });
    });
  }

  // ── Append a bubble ───────────────────────────────────────────
  function appendBubble(role, text, ts, isThinking, skipScroll) {
    var isUser  = role === 'user';
    var row     = document.createElement('div');
    row.className = 'chat-row' + (isUser ? ' chat-row-user' : '');

    var avatarHtml = isUser
      ? '<div class="chat-avatar chat-avatar-user">You</div>'
      : '<div class="chat-avatar chat-avatar-delka"><img src="/images/logo.svg" width="18" height="18" alt="Delka"></div>';

    var senderName = isUser ? 'You' : 'Delka';
    var bubbleClass = 'chat-bubble ' + (isUser ? 'chat-bubble-user' : 'chat-bubble-delka') + (isThinking ? ' chat-bubble-thinking' : '');

    var bubbleContent;
    if (isThinking) {
      bubbleContent = delkaSpinnerSvg('ct');
    } else if (isUser) {
      bubbleContent = escHtml(text);
    } else {
      bubbleContent = marked.parse(text || '');
    }

    var metaHtml = '<div class="chat-meta">' + (ts ? formatTime(ts) + ' · ' + MODEL_NAME : '') + '</div>';

    row.innerHTML =
      avatarHtml +
      '<div class="chat-bubble-wrap">' +
        '<div class="chat-sender">' + senderName + '</div>' +
        '<div class="' + bubbleClass + '">' + bubbleContent + '</div>' +
        metaHtml +
      '</div>';

    messagesEl.appendChild(row);
    if (!skipScroll) messagesEl.scrollTop = messagesEl.scrollHeight;
    return row.querySelector('.chat-bubble');
  }

  // ── Typewriter ────────────────────────────────────────────────
  function typewriterReveal(bubble, text, onDone) {
    var words = text.split(' '), i = 0, current = '';
    function step() {
      if (i >= words.length) {
        bubble.innerHTML = marked.parse(text);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        if (onDone) onDone();
        return;
      }
      current += (i === 0 ? '' : ' ') + words[i++];
      bubble.innerHTML = marked.parse(current);
      messagesEl.scrollTop = messagesEl.scrollHeight;
      setTimeout(step, 16);
    }
    step();
  }

  // ── Utils ─────────────────────────────────────────────────────
  function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function formatTime(ts) {
    return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  function autoResize() {
    inputEl.style.height = 'auto';
    inputEl.style.height = Math.min(inputEl.scrollHeight, 140) + 'px';
  }

  // ── Send message ──────────────────────────────────────────────
  function sendMessage() {
    var msg = inputEl.value.trim();
    if (!msg) return;
    inputEl.value = '';
    inputEl.style.height = 'auto';

    var store = loadStore();

    // Ensure active conversation
    if (!store.activeId || !store.conversations[store.activeId]) {
      createConv(store);
    }
    var convId = store.activeId;
    var conv   = store.conversations[convId];

    // Remove empty state
    var emptyEl = messagesEl.querySelector('.chat-empty');
    if (emptyEl) emptyEl.remove();

    // Auto-title from first user message
    if (conv.messages.length === 0) {
      setTitle(store, convId, msg);
      topbarTitle.textContent = conv.title;
    }

    var ts = Date.now();
    conv.messages.push({ role: 'user', text: msg, ts: ts });
    conv.updatedAt = ts;
    saveStore(store);
    renderHistory(store);

    appendBubble('user', msg, ts);

    // Thinking indicator
    var thinkingBubble = appendBubble('assistant', '', null, true);
    sendBtn.disabled = true;

    fetch('/general-chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg, session_id: backendSession }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var replyTs = Date.now();
      if (data.error) {
        thinkingBubble.className = 'chat-bubble chat-bubble-delka chat-bubble-error';
        thinkingBubble.textContent = 'Error: ' + data.error;
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return;
      }
      var reply = data.reply || '(no reply)';
      if (data.session_id) saveBackendSession(data.session_id);

      thinkingBubble.className = 'chat-bubble chat-bubble-delka';
      thinkingBubble.style.cssText = '';
      thinkingBubble.innerHTML = '';

      var metaEl = thinkingBubble.closest('.chat-bubble-wrap').querySelector('.chat-meta');
      if (metaEl) metaEl.textContent = formatTime(replyTs) + ' · ' + MODEL_NAME;

      typewriterReveal(thinkingBubble, reply, function() {
        var s2 = loadStore();
        if (s2.conversations[convId]) {
          s2.conversations[convId].messages.push({ role: 'assistant', text: reply, ts: replyTs });
          s2.conversations[convId].updatedAt = replyTs;
          saveStore(s2);
          renderHistory(s2);
        }
      });
    })
    .catch(function(err) {
      thinkingBubble.className = 'chat-bubble chat-bubble-delka chat-bubble-error';
      thinkingBubble.textContent = 'Connection error: ' + err.message;
      messagesEl.scrollTop = messagesEl.scrollHeight;
    })
    .finally(function() { sendBtn.disabled = false; inputEl.focus(); });
  }

  // ── Event listeners ───────────────────────────────────────────
  inputEl.addEventListener('input', autoResize);
  inputEl.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });
  sendBtn.addEventListener('click', sendMessage);

  newChatBtn.addEventListener('click', function() {
    var store = loadStore();
    createConv(store);
    saveStore(store);
    renderHistory(store);
    showEmpty();
    topbarTitle.textContent = 'New Chat';
    backendSession = 'console-' + Math.random().toString(36).slice(2);
    localStorage.setItem(SESSION_KEY, backendSession);
    inputEl.focus();
  });

  historyToggle.addEventListener('click', function() {
    if (window.innerWidth <= 768) {
      convPanel.classList.toggle('cp-mobile-open');
    } else {
      convPanel.classList.toggle('cp-hidden');
    }
  });

  // ── Init ──────────────────────────────────────────────────────
  (function init() {
    var store = loadStore();
    if (!store.activeId || !store.conversations[store.activeId]) {
      // No active conversation — show empty state, don't auto-create
      renderHistory(store);
      showEmpty();
    } else {
      renderHistory(store);
      renderMessages(store.conversations[store.activeId]);
      topbarTitle.textContent = store.conversations[store.activeId].title;
    }
    inputEl.focus();
  })();

}());
</script>
</body>
</html>
