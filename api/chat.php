<?php
/**
 * Developer Support Chat — dedicated full-page chat interface.
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
<title>AI Chat — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
<style>
/* ── Chat page layout ──────────────────────────────────────── */
.chat-page {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 0px);
  max-width: 820px;
  margin: 0 auto;
  width: 100%;
  padding: 0 24px;
  box-sizing: border-box;
}

.chat-page-header {
  padding: 28px 0 16px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.chat-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }
.chat-page-header p  { font-size: 13px; color: var(--muted); margin: 4px 0 0; }

.chat-clear-btn {
  font-size: 12px;
  color: var(--muted);
  background: none;
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 5px 12px;
  cursor: pointer;
  flex-shrink: 0;
  transition: color .15s, border-color .15s;
}
.chat-clear-btn:hover { color: var(--text); border-color: var(--muted); }

/* ── Message list ───────────────────────────────────────────── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 24px 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
  min-height: 0;
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
  gap: 12px;
  color: var(--muted);
  text-align: center;
  padding: 40px 0;
}
.chat-empty svg { opacity: .3; }
.chat-empty h3 { font-size: 16px; font-weight: 600; margin: 0; color: var(--text); }
.chat-empty p  { font-size: 13px; margin: 0; max-width: 340px; line-height: 1.6; }

/* Suggestion chips */
.chat-suggestions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-top: 8px;
}
.chat-suggestion {
  font-size: 12px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 6px 14px;
  cursor: pointer;
  color: var(--text);
  transition: border-color .15s, background .15s;
}
.chat-suggestion:hover { border-color: var(--accent); background: var(--surface); }

/* Message rows */
.chat-row {
  display: flex;
  gap: 10px;
  align-items: flex-start;
}
.chat-row.chat-row-user { flex-direction: row-reverse; }

.chat-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
}
.chat-avatar-ai   { background: var(--accent); color: #fff; }
.chat-avatar-user { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }

.chat-bubble {
  display: inline-block;
  max-width: 100%;
  padding: 10px 14px;
  border-radius: 14px;
  font-size: 13px;
  line-height: 1.65;
  white-space: pre-wrap;
  word-break: break-word;
}
.chat-row > div {
  max-width: 72%;
  display: flex;
  flex-direction: column;
}
.chat-bubble-ai {
  background: var(--surface2);
  color: var(--text);
  border-bottom-left-radius: 4px;
}
.chat-bubble-user {
  background: var(--accent);
  color: #fff;
  border-bottom-right-radius: 4px;
}
.chat-bubble-error {
  background: #7f1d1d;
  color: #fca5a5;
}

.chat-bubble.thinking {
  opacity: .55;
  font-style: italic;
}

.chat-meta {
  font-size: 11px;
  color: var(--muted);
  margin-top: 4px;
  padding: 0 4px;
}
.chat-row-user .chat-meta { text-align: right; }

/* ── Input bar ──────────────────────────────────────────────── */
.chat-input-bar {
  flex-shrink: 0;
  padding: 12px 0 20px;
  border-top: 1px solid var(--border);
}
.chat-input-wrap {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 10px 12px;
  transition: border-color .15s;
}
.chat-input-wrap:focus-within { border-color: var(--accent); }
#chat-input {
  flex: 1;
  background: none;
  border: none;
  color: var(--text);
  font-size: 13px;
  font-family: inherit;
  resize: none;
  line-height: 1.5;
  max-height: 120px;
  overflow-y: auto;
  outline: none;
}
#chat-input::placeholder { color: var(--muted); }
.chat-send-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: var(--accent);
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background .15s, transform .1s;
}
.chat-send-btn:hover:not(:disabled) { background: #6d28d9; transform: scale(1.05); }
.chat-send-btn:disabled { opacity: .45; cursor: default; transform: none; }
.chat-hint { font-size: 11px; color: var(--muted); margin-top: 6px; text-align: center; }
</style>
</head>
<body>
<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content" style="padding:0;display:flex;flex-direction:column;min-height:100vh;">
    <div class="chat-page">

      <div class="chat-page-header">
        <div>
          <h1>AI Chat</h1>
          <p>A general-purpose AI assistant. Ask anything.</p>
        </div>
        <button class="chat-clear-btn" id="chat-clear-btn" title="Clear conversation">Clear chat</button>
      </div>

      <div class="chat-messages" id="chat-messages">
        <!-- empty state shown by JS -->
      </div>

      <div class="chat-input-bar">
        <form id="chat-form" autocomplete="off">
          <div class="chat-input-wrap">
            <textarea
              id="chat-input"
              rows="1"
              placeholder="Ask anything..."
              aria-label="Chat message"
            ></textarea>
            <button type="submit" class="chat-send-btn" id="chat-send-btn" aria-label="Send">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
          </div>
          <p class="chat-hint">Press Enter to send &nbsp;·&nbsp; Shift+Enter for a new line</p>
        </form>
      </div>

    </div>
  </main>
</div>

<script src="/js/app.js"></script>
<script>
(function () {
  var STORAGE_KEY  = 'delkai_chat_history';
  var SESSION_KEY  = 'delkai_chat_session';
  var MAX_HISTORY  = 100;

  var messagesEl = document.getElementById('chat-messages');
  var form       = document.getElementById('chat-form');
  var input      = document.getElementById('chat-input');
  var sendBtn    = document.getElementById('chat-send-btn');
  var clearBtn   = document.getElementById('chat-clear-btn');

  // ── Session ─────────────────────────────────────────────────
  function getSession() {
    var s = localStorage.getItem(SESSION_KEY);
    if (!s) { s = 'console-' + Math.random().toString(36).slice(2); localStorage.setItem(SESSION_KEY, s); }
    return s;
  }
  function saveSession(id) { localStorage.setItem(SESSION_KEY, id); }

  var session = getSession();

  // ── Persisted history ────────────────────────────────────────
  function loadHistory() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch(e) { return []; }
  }
  function saveHistory(h) {
    if (h.length > MAX_HISTORY) h = h.slice(-MAX_HISTORY);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(h));
  }

  // ── Render ───────────────────────────────────────────────────
  var SUGGESTIONS = [
    'Write a Python function to reverse a linked list',
    'Explain async/await in JavaScript',
    'What\'s the difference between SQL and NoSQL?',
    'Help me write a cover letter opening',
    'Summarise the key ideas in clean code',
  ];

  function showEmpty() {
    messagesEl.innerHTML =
      '<div class="chat-empty">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
        '<h3>DelkaAI Chat</h3>' +
        '<p>A general-purpose AI assistant. Ask about code, writing, ideas — anything.</p>' +
        '<div class="chat-suggestions">' +
          SUGGESTIONS.map(function(s) {
            return '<button class="chat-suggestion" type="button">' + s + '</button>';
          }).join('') +
        '</div>' +
      '</div>';

    messagesEl.querySelectorAll('.chat-suggestion').forEach(function(btn) {
      btn.addEventListener('click', function() {
        input.value = btn.textContent;
        autoResize();
        submitMessage();
      });
    });
  }

  function formatTime(ts) {
    var d = new Date(ts);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function appendBubble(role, text, ts, thinking) {
    var isUser = role === 'user';
    var row = document.createElement('div');
    row.className = 'chat-row' + (isUser ? ' chat-row-user' : '');
    row.innerHTML =
      '<div class="chat-avatar ' + (isUser ? 'chat-avatar-user' : 'chat-avatar-ai') + '">' +
        (isUser ? 'You' : 'AI') +
      '</div>' +
      '<div>' +
        '<div class="chat-bubble ' + (isUser ? 'chat-bubble-user' : 'chat-bubble-ai') + (thinking ? ' thinking' : '') + '">' +
          escapeHtml(text) +
        '</div>' +
        (ts ? '<div class="chat-meta">' + formatTime(ts) + '</div>' : '') +
      '</div>';
    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return row.querySelector('.chat-bubble');
  }

  function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  // ── Load history on page open ────────────────────────────────
  var history = loadHistory();
  if (history.length === 0) {
    showEmpty();
  } else {
    history.forEach(function(m) { appendBubble(m.role, m.text, m.ts); });
  }

  // ── Auto-resize textarea ─────────────────────────────────────
  function autoResize() {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
  }
  input.addEventListener('input', autoResize);

  // ── Keyboard shortcut ────────────────────────────────────────
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    }
  });

  // ── Send message ─────────────────────────────────────────────
  function submitMessage() {
    var msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    input.style.height = 'auto';

    // Remove empty state
    var empty = messagesEl.querySelector('.chat-empty');
    if (empty) empty.remove();

    var ts = Date.now();
    appendBubble('user', msg, ts);
    var h = loadHistory();
    h.push({ role: 'user', text: msg, ts: ts });
    saveHistory(h);

    var thinkingBubble = appendBubble('assistant', '...', null, true);
    sendBtn.disabled = true;

    fetch('/general-chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg, session_id: session }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var replyTs = Date.now();
      if (data.error) {
        thinkingBubble.className = 'chat-bubble chat-bubble-ai chat-bubble-error';
        thinkingBubble.textContent = 'Error: ' + data.error;
      } else {
        var reply = data.reply || '(no reply)';
        thinkingBubble.className = 'chat-bubble chat-bubble-ai';
        thinkingBubble.textContent = reply;
        thinkingBubble.closest('.chat-row').querySelector('.chat-meta') ||
          thinkingBubble.insertAdjacentHTML('afterend', '<div class="chat-meta">' + formatTime(replyTs) + '</div>');

        var h2 = loadHistory();
        h2.push({ role: 'assistant', text: reply, ts: replyTs });
        saveHistory(h2);

        if (data.session_id) { session = data.session_id; saveSession(session); }
      }
      messagesEl.scrollTop = messagesEl.scrollHeight;
    })
    .catch(function(err) {
      thinkingBubble.className = 'chat-bubble chat-bubble-ai chat-bubble-error';
      thinkingBubble.textContent = 'Connection error: ' + err.message;
      messagesEl.scrollTop = messagesEl.scrollHeight;
    })
    .finally(function() { sendBtn.disabled = false; input.focus(); });
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitMessage();
  });

  // ── Clear chat ───────────────────────────────────────────────
  clearBtn.addEventListener('click', function() {
    if (!confirm('Clear this conversation?')) return;
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(SESSION_KEY);
    session = getSession();
    messagesEl.innerHTML = '';
    showEmpty();
  });

  input.focus();
}());
</script>
</body>
</html>
