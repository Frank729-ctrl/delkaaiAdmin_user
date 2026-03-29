<?php
/**
 * DelkaAI Chat — Atelier-style layout with conversation history.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

$active_page = 'chat';
?><!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat — Delka AI</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "outline-variant": "#afb3b0",
          "secondary": "#605f57",
          "on-background": "#2f3331",
          "surface-variant": "#e0e3e0",
          "primary-container": "#e6e2de",
          "surface-container-lowest": "#ffffff",
          "surface-container": "#edeeeb",
          "on-tertiary": "#fff7f5",
          "inverse-surface": "#0d0e0e",
          "surface-container-high": "#e6e9e6",
          "on-surface": "#2f3331",
          "tertiary": "#9a462a",
          "surface-container-low": "#f3f4f1",
          "secondary-container": "#e5e2d8",
          "outline": "#777c79",
          "on-primary": "#fcf7f3",
          "surface-dim": "#d6dbd7",
          "on-surface-variant": "#5c605d",
          "background": "#faf9f7",
          "surface": "#faf9f7",
          "error": "#9e422c",
          "primary": "#605e5b",
          "primary-fixed": "#e6e2de",
        },
        fontFamily: {
          "headline": ["Newsreader", "serif"],
          "body": ["Inter", "sans-serif"],
        },
        borderRadius: {
          DEFAULT: "0.125rem",
          lg: "0.25rem",
          xl: "0.5rem",
          full: "0.75rem",
        },
      },
    },
  }
</script>
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
  }
  body { font-family: 'Inter', sans-serif; background: #faf9f7; color: #2f3331; }
  .serif-text { font-family: 'Newsreader', serif; }

  /* Scrollbars */
  #chat-scroll::-webkit-scrollbar { width: 3px; }
  #chat-scroll::-webkit-scrollbar-thumb { background: #d6dbd7; border-radius: 2px; }
  #conv-list::-webkit-scrollbar { width: 3px; }
  #conv-list::-webkit-scrollbar-thumb { background: #d6dbd7; border-radius: 2px; }

  /* Thinking dots */
  @keyframes blink { 0%,80%,100% { opacity: 0.2; } 40% { opacity: 1; } }
  .thinking-dot { display: inline-block; width: 6px; height: 6px; background: #605e5b; border-radius: 50%; margin: 0 2px; animation: blink 1.4s infinite; }
  .thinking-dot:nth-child(2) { animation-delay: 0.2s; }
  .thinking-dot:nth-child(3) { animation-delay: 0.4s; }

  /* Conv item active state */
  .conv-item.active { background: #f3f4f1; color: #2f3331; }
  .conv-item.active .conv-icon { color: #605e5b; }
  .conv-item.active::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; height: 60%; width: 2px;
    background: #605e5b; border-radius: 0 2px 2px 0;
  }

  /* Sidebar mobile */
  @media (max-width: 768px) {
    #conv-panel { transform: translateX(-100%); transition: transform .2s ease; }
    #conv-panel.cp-mobile-open { transform: translateX(0); }
    #conv-panel.cp-hidden { display: none; }
    .chat-main-area { margin-left: 0 !important; }
  }
  @media (min-width: 769px) {
    #conv-panel.cp-hidden { width: 0; overflow: hidden; padding: 0; border: none; }
    .chat-main-area { transition: margin-left .2s ease; }
    #conv-panel { transition: width .2s ease; }
  }

  /* Markdown prose inside AI bubbles */
  .delka-prose p { margin: 0 0 0.65em; }
  .delka-prose p:last-child { margin-bottom: 0; }
  .delka-prose h1,.delka-prose h2,.delka-prose h3 {
    font-family: 'Newsreader', serif; font-weight: 600; margin: 1em 0 0.4em;
  }
  .delka-prose h1 { font-size: 1.35em; }
  .delka-prose h2 { font-size: 1.15em; }
  .delka-prose h3 { font-size: 1.05em; }
  .delka-prose ul,.delka-prose ol { margin: 0.4em 0 0.7em; padding-left: 1.4em; }
  .delka-prose li { margin-bottom: 0.3em; }
  .delka-prose code {
    background: #e6e9e6; border: 1px solid rgba(175,179,176,0.4);
    border-radius: 3px; padding: 1px 5px; font-size: 0.85em; font-family: monospace;
  }
  .delka-prose pre {
    background: #e6e9e6; border: 1px solid rgba(175,179,176,0.4);
    border-radius: 6px; padding: 12px 16px; overflow-x: auto; margin: 0.8em 0;
  }
  .delka-prose pre code { background: none; border: none; padding: 0; }
  .delka-prose strong { font-weight: 700; }
  .delka-prose em { font-style: italic; }
  .delka-prose blockquote {
    border-left: 2px solid #9a462a; margin: 0.6em 0;
    padding: 4px 0 4px 12px; color: #5c605d;
  }
  .delka-prose table { border-collapse: collapse; width: 100%; margin: 0.6em 0; font-size: 0.88em; }
  .delka-prose th,.delka-prose td { border: 1px solid rgba(175,179,176,0.4); padding: 6px 10px; text-align: left; }
  .delka-prose th { background: #edeeeb; font-weight: 600; }
  .delka-prose hr { border: none; border-top: 1px solid #e0e3e0; margin: 1em 0; }
  .delka-prose-error { color: #9e422c !important; }
</style>
</head>
<body class="flex min-h-screen">

<!-- ── Sidebar ───────────────────────────────────────────────── -->
<aside id="conv-panel" class="fixed left-0 top-0 h-screen w-72 flex flex-col p-6 z-50 bg-white/85 backdrop-blur-xl border-r border-[#afb3b0]/10 shadow-[0_12px_32px_-4px_rgba(47,51,49,0.06)]">

  <!-- Brand -->
  <div class="mb-8 flex-shrink-0">
    <a href="/dashboard" class="flex items-center gap-3 group">
      <div class="w-8 h-8 bg-[#605e5b] rounded-sm flex items-center justify-center text-white font-['Newsreader'] italic font-bold text-sm flex-shrink-0">D</div>
      <div>
        <h1 class="font-['Newsreader'] text-lg font-medium text-[#2f3331] leading-none">Delka AI</h1>
        <p class="text-[9px] uppercase tracking-[0.2em] text-[#5c605d] opacity-60 mt-0.5">Console</p>
      </div>
    </a>
  </div>

  <!-- New Chat -->
  <button id="new-chat-btn"
    class="mb-6 flex-shrink-0 w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-[#605e5b] text-[#fcf7f3] rounded-sm text-sm tracking-wide font-medium transition-all active:scale-95 hover:bg-[#53514f] shadow-sm">
    <span class="material-symbols-outlined" style="font-size:16px;">add_notes</span>
    New Chat
  </button>

  <!-- Conversation list -->
  <div id="conv-list" class="flex-1 overflow-y-auto -mx-1 px-1 min-h-0"></div>

  <!-- Nav links -->
  <div class="flex-shrink-0 pt-4 border-t border-[#afb3b0]/10 space-y-0.5 mt-2">
    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 text-[#5c605d] hover:bg-[#f3f4f1] rounded-sm transition-all text-[0.8rem]">
      <span class="material-symbols-outlined text-[#afb3b0]" style="font-size:17px;">grid_view</span>
      Dashboard
    </a>
    <a href="/playground" class="flex items-center gap-3 px-3 py-2 text-[#5c605d] hover:bg-[#f3f4f1] rounded-sm transition-all text-[0.8rem]">
      <span class="material-symbols-outlined text-[#afb3b0]" style="font-size:17px;">science</span>
      Playground
    </a>
    <a href="/docs" class="flex items-center gap-3 px-3 py-2 text-[#5c605d] hover:bg-[#f3f4f1] rounded-sm transition-all text-[0.8rem]">
      <span class="material-symbols-outlined text-[#afb3b0]" style="font-size:17px;">book_5</span>
      Docs
    </a>
    <a href="/usage" class="flex items-center gap-3 px-3 py-2 text-[#5c605d] hover:bg-[#f3f4f1] rounded-sm transition-all text-[0.8rem]">
      <span class="material-symbols-outlined text-[#afb3b0]" style="font-size:17px;">bar_chart</span>
      Usage
    </a>
  </div>
</aside>

<!-- ── Main ──────────────────────────────────────────────────── -->
<main id="chat-main-area" class="chat-main-area flex-1 ml-72 flex flex-col h-screen relative overflow-hidden">

  <!-- Header -->
  <header class="flex justify-between items-center px-10 py-4 sticky top-0 z-40 bg-[#faf9f7] flex-shrink-0">
    <div class="flex items-center gap-3">
      <button id="history-toggle"
        class="p-1.5 text-[#5c605d] hover:text-[#2f3331] hover:bg-[#f3f4f1] rounded-sm transition-all">
        <span class="material-symbols-outlined" style="font-size:20px;">menu</span>
      </button>
      <h2 id="chat-topbar-title" class="font-['Newsreader'] italic text-xl tracking-tight text-[#605e5b] truncate max-w-xs">
        New Chat
      </h2>
    </div>
    <div class="flex items-center gap-5">
      <a href="/docs" class="flex items-center gap-1.5 text-[#5c605d] hover:text-[#2f3331] transition-colors">
        <span class="material-symbols-outlined" style="font-size:18px;">help</span>
        <span class="text-[10px] font-medium tracking-widest uppercase">Support</span>
      </a>
      <span class="text-[10px] bg-[#edeeeb] border border-[#afb3b0]/20 text-[#5c605d] px-3 py-1 rounded-full tracking-wide hidden sm:block">Delka Spark 1.0</span>
    </div>
  </header>

  <!-- Messages scroll container -->
  <div id="chat-scroll" class="flex-1 overflow-y-auto pb-44">
    <div id="chat-messages" class="max-w-3xl mx-auto px-10 py-10 space-y-8">
      <!-- populated by JS -->
    </div>
  </div>

  <!-- Input composition area -->
  <div class="absolute bottom-0 left-0 w-full px-10 pb-7 bg-gradient-to-t from-[#faf9f7] via-[#faf9f7]/95 to-transparent pt-8">
    <div class="max-w-3xl mx-auto">
      <div class="relative bg-white rounded-xl shadow-[0_8px_32px_-4px_rgba(47,51,49,0.1)] border border-[#afb3b0]/15 focus-within:ring-1 focus-within:ring-[#605e5b]/25 transition-all">
        <textarea id="chat-input" rows="1"
          placeholder="Message Delka AI…"
          class="w-full bg-transparent border-none outline-none focus:ring-0 text-[#2f3331] text-[0.9rem] leading-relaxed px-5 py-4 pr-16 resize-none placeholder-[#afb3b0]/70 max-h-36 overflow-y-auto"></textarea>
        <div class="absolute right-3 bottom-3">
          <button type="button" id="chat-send-btn"
            class="bg-[#605e5b] text-white p-2 rounded-sm shadow-sm transition-all active:scale-95 hover:bg-[#53514f] disabled:opacity-30 disabled:cursor-default flex items-center justify-center">
            <span class="material-symbols-outlined" style="font-size:17px;">arrow_upward</span>
          </button>
        </div>
      </div>
      <p class="text-[9.5px] text-center mt-2.5 text-[#5c605d]/40 uppercase tracking-[0.2em]">
        Enter to send &nbsp;·&nbsp; Shift+Enter for new line
      </p>
    </div>
  </div>

</main>

<script>
(function () {
  'use strict';

  // ── Constants ──────────────────────────────────────────────────
  var STORE_KEY   = 'delkai_conversations_v2';
  var SESSION_KEY = 'delkai_chat_session';
  var MODEL_NAME  = 'Delka Spark 1.0';
  var SUGGESTIONS = [
    'Help me write a CV for a software engineering role in Ghana',
    'What are the top companies hiring in Accra right now?',
    'Write a cover letter for a banking position',
    'How do I negotiate salary with a Ghanaian employer?',
    'Explain REST APIs like I\'m a junior developer',
  ];

  // ── DOM refs ───────────────────────────────────────────────────
  var chatScroll    = document.getElementById('chat-scroll');
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
  function loadStore() {
    try { return JSON.parse(localStorage.getItem(STORE_KEY) || 'null') || { conversations: {}, activeId: null }; }
    catch(e) { return { conversations: {}, activeId: null }; }
  }
  function saveStore(s) { localStorage.setItem(STORE_KEY, JSON.stringify(s)); }
  function newConvId() { return 'c-' + Date.now() + '-' + Math.random().toString(36).slice(2,6); }

  function createConv(store) {
    var id = newConvId();
    store.conversations[id] = { id: id, title: 'New Chat', messages: [], createdAt: Date.now(), updatedAt: Date.now() };
    store.activeId = id;
    return id;
  }

  function setTitle(store, id, text) {
    if (!store.conversations[id]) return;
    store.conversations[id].title = text.trim().slice(0, 48) || 'New Chat';
  }

  // ── Session (backend) ──────────────────────────────────────────
  var backendSession = localStorage.getItem(SESSION_KEY) || ('console-' + Math.random().toString(36).slice(2));
  function saveBackendSession(id) { backendSession = id; localStorage.setItem(SESSION_KEY, id); }

  // ── History panel render ───────────────────────────────────────
  function formatGroupLabel(ts) {
    var diff = Math.floor((Date.now() - ts) / 86400000);
    if (diff === 0) return 'Today';
    if (diff === 1) return 'Yesterday';
    if (diff <= 6) return 'Last 7 days';
    if (diff <= 29) return 'Last 30 days';
    return new Date(ts).toLocaleString('default', { month: 'long', year: 'numeric' });
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
      html += '<p class="text-[9px] font-semibold uppercase tracking-[0.15em] text-[#afb3b0] px-3 pt-4 pb-1 first:pt-2">' + escHtml(g) + '</p>';
      groups[g].forEach(function(c) {
        var active = c.id === store.activeId ? ' active' : '';
        html += '<div class="conv-item relative flex items-center gap-2 px-3 py-2 text-[#5c605d] hover:bg-[#f3f4f1] rounded-sm cursor-pointer transition-all text-[0.78rem] group' + active + '" data-id="' + c.id + '">'
          + '<span class="conv-icon material-symbols-outlined text-[#c5c9c6] flex-shrink-0" style="font-size:13px;">chat_bubble</span>'
          + '<span class="flex-1 truncate leading-relaxed">' + escHtml(c.title) + '</span>'
          + '<button class="conv-item-del opacity-0 group-hover:opacity-100 bg-transparent border-none text-[#c5c9c6] hover:text-[#9e422c] cursor-pointer flex-shrink-0 text-base leading-none transition-all px-0.5" data-del="' + c.id + '" title="Delete">×</button>'
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
    chatScroll.scrollTop = chatScroll.scrollHeight;
  }

  // ── Empty state ────────────────────────────────────────────────
  function showEmpty() {
    messagesEl.innerHTML =
      '<section class="chat-empty mb-16 pt-4">' +
        '<h1 class="serif-text text-5xl font-light text-[#605e5b] leading-tight max-w-xl">' +
          'Your career, your story — <em>let\'s write it well.</em>' +
        '</h1>' +
        '<p class="mt-5 text-[#5c605d] text-lg max-w-md leading-relaxed font-light">' +
          'Welcome to Delka AI. Ask about CVs, jobs, business, code — or anything at all.' +
        '</p>' +
        '<div class="flex flex-wrap gap-2 mt-8">' +
          SUGGESTIONS.map(function(s) {
            return '<button class="chat-suggestion bg-white border border-[#afb3b0]/25 text-[#5c605d] text-[0.78rem] px-4 py-2.5 rounded-sm hover:border-[#605e5b]/40 hover:text-[#2f3331] transition-all text-left leading-relaxed shadow-sm">' + escHtml(s) + '</button>';
          }).join('') +
        '</div>' +
      '</section>';

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
    var isUser = role === 'user';
    var row    = document.createElement('div');

    if (isThinking) {
      row.innerHTML =
        '<div class="py-6 px-8 bg-[#f3f4f1]/60 rounded-[0.75rem] border border-[#afb3b0]/10">' +
          '<div class="flex items-center gap-0.5 h-5">' +
            '<span class="thinking-dot"></span>' +
            '<span class="thinking-dot"></span>' +
            '<span class="thinking-dot"></span>' +
          '</div>' +
        '</div>';

    } else if (isUser) {
      row.className = 'flex flex-col items-end gap-2';
      row.innerHTML =
        '<div class="bg-[#f3f4f1] px-5 py-3.5 rounded-xl max-w-[80%] shadow-sm">' +
          '<p class="text-[#2f3331] text-[0.9rem] leading-relaxed whitespace-pre-wrap">' + escHtml(text) + '</p>' +
        '</div>' +
        '<span class="text-[10px] uppercase tracking-widest text-[#5c605d]/40 px-1">' +
          (ts ? formatTime(ts) : '') +
        '</span>';

    } else {
      // AI bubble
      var content = text ? marked.parse(text) : '';
      var metaText = ts ? formatTime(ts) + ' · ' + MODEL_NAME : '';
      row.innerHTML =
        '<div class="py-7 px-8 bg-[#f3f4f1]/50 rounded-[0.75rem] border border-[#afb3b0]/10">' +
          '<div class="chat-bubble delka-prose serif-text text-[1.02rem] leading-[1.8] text-[#2f3331]">' + content + '</div>' +
          '<div class="flex items-center justify-between mt-5 pt-3.5 border-t border-[#afb3b0]/10">' +
            '<span class="chat-meta text-[10px] text-[#5c605d]/40 uppercase tracking-widest">' + metaText + '</span>' +
            '<div class="flex items-center gap-4">' +
              '<button class="copy-btn flex items-center gap-1 text-[10px] uppercase tracking-widest text-[#5c605d]/50 hover:text-[#2f3331] transition-colors">' +
                '<span class="material-symbols-outlined" style="font-size:13px;">content_copy</span>Copy' +
              '</button>' +
            '</div>' +
          '</div>' +
        '</div>';

      // Copy button handler
      var copyBtn = row.querySelector('.copy-btn');
      var fullText = text;
      if (copyBtn) {
        copyBtn.addEventListener('click', function() {
          navigator.clipboard.writeText(fullText || '').then(function() {
            copyBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:13px;">check</span>Copied';
            setTimeout(function() {
              copyBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:13px;">content_copy</span>Copy';
            }, 2000);
          });
        });
      }
    }

    messagesEl.appendChild(row);
    if (!skipScroll) chatScroll.scrollTop = chatScroll.scrollHeight;

    // Return the bubble element (for streaming updates)
    if (isThinking) return row.querySelector('.chat-bubble') || row.firstElementChild;
    if (!isUser)    return row.querySelector('.chat-bubble');
    return null;
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
    inputEl.style.height = Math.min(inputEl.scrollHeight, 144) + 'px';
  }

  // ── Send message (streaming) ──────────────────────────────────
  function sendMessage() {
    var msg = inputEl.value.trim();
    if (!msg) return;
    inputEl.value = '';
    inputEl.style.height = 'auto';

    var store = loadStore();

    if (!store.activeId || !store.conversations[store.activeId]) {
      createConv(store);
    }
    var convId = store.activeId;
    var conv   = store.conversations[convId];

    var emptyEl = messagesEl.querySelector('.chat-empty');
    if (emptyEl) emptyEl.remove();

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

    // Thinking row
    var thinkingRow = document.createElement('div');
    thinkingRow.innerHTML =
      '<div class="py-6 px-8 bg-[#f3f4f1]/60 rounded-[0.75rem] border border-[#afb3b0]/10">' +
        '<div class="flex items-center gap-0.5 h-5">' +
          '<span class="thinking-dot"></span><span class="thinking-dot"></span><span class="thinking-dot"></span>' +
        '</div>' +
      '</div>';
    messagesEl.appendChild(thinkingRow);
    chatScroll.scrollTop = chatScroll.scrollHeight;
    sendBtn.disabled = true;

    var fullReply    = '';
    var replyTs      = Date.now();
    var streamStarted = false;
    var aiBubble     = null; // the .chat-bubble div once streaming starts
    var metaSpan     = null;

    fetch('/general-chat-stream', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg, session_id: backendSession }),
    })
    .then(function(response) {
      if (!response.ok) {
        return response.text().then(function(t) { throw new Error(t || response.statusText); });
      }
      var reader  = response.body.getReader();
      var decoder = new TextDecoder();
      var buffer  = '';

      function pump() {
        return reader.read().then(function(result) {
          if (result.done) {
            var s2 = loadStore();
            if (s2.conversations[convId]) {
              s2.conversations[convId].messages.push({ role: 'assistant', text: fullReply, ts: replyTs });
              s2.conversations[convId].updatedAt = replyTs;
              saveStore(s2);
              renderHistory(s2);
            }
            // Wire up copy button with final text
            if (aiBubble) {
              var copyBtn = thinkingRow.querySelector('.copy-btn');
              if (copyBtn) {
                var capturedReply = fullReply;
                copyBtn.onclick = function() {
                  navigator.clipboard.writeText(capturedReply).then(function() {
                    copyBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:13px;">check</span>Copied';
                    setTimeout(function() {
                      copyBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:13px;">content_copy</span>Copy';
                    }, 2000);
                  });
                };
              }
            }
            sendBtn.disabled = false;
            inputEl.focus();
            return;
          }

          buffer += decoder.decode(result.value, { stream: true });
          var lines = buffer.split('\n');
          buffer = lines.pop();

          lines.forEach(function(line) {
            if (!line.startsWith('data: ')) return;
            var payload = line.slice(6).trim();
            if (!payload || payload === '[DONE]') return;

            if (payload.charAt(0) === '{') {
              try {
                var meta = JSON.parse(payload);
                if (meta.session_id) saveBackendSession(meta.session_id);
              } catch(e) {}
              return;
            }

            // First token — replace thinking indicator with AI bubble
            if (!streamStarted) {
              streamStarted = true;
              thinkingRow.innerHTML =
                '<div class="py-7 px-8 bg-[#f3f4f1]/50 rounded-[0.75rem] border border-[#afb3b0]/10">' +
                  '<div class="chat-bubble delka-prose serif-text text-[1.02rem] leading-[1.8] text-[#2f3331]"></div>' +
                  '<div class="flex items-center justify-between mt-5 pt-3.5 border-t border-[#afb3b0]/10">' +
                    '<span class="chat-meta text-[10px] text-[#5c605d]/40 uppercase tracking-widest">' + formatTime(replyTs) + ' · ' + MODEL_NAME + '</span>' +
                    '<div class="flex items-center gap-4">' +
                      '<button class="copy-btn flex items-center gap-1 text-[10px] uppercase tracking-widest text-[#5c605d]/50 hover:text-[#2f3331] transition-colors">' +
                        '<span class="material-symbols-outlined" style="font-size:13px;">content_copy</span>Copy' +
                      '</button>' +
                    '</div>' +
                  '</div>' +
                '</div>';
              aiBubble = thinkingRow.querySelector('.chat-bubble');
            }

            fullReply += payload;
            if (aiBubble) {
              aiBubble.innerHTML = marked.parse(fullReply);
              chatScroll.scrollTop = chatScroll.scrollHeight;
            }
          });

          return pump();
        });
      }

      return pump();
    })
    .catch(function(err) {
      thinkingRow.innerHTML =
        '<div class="py-5 px-8 bg-[#f3f4f1]/50 rounded-[0.75rem] border border-[#afb3b0]/10">' +
          '<p class="text-[#9e422c] text-sm">Connection error: ' + escHtml(err.message) + '</p>' +
        '</div>';
      chatScroll.scrollTop = chatScroll.scrollHeight;
      sendBtn.disabled = false;
      inputEl.focus();
    });
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
    messagesEl.innerHTML = '';
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
      if (convPanel.classList.toggle('cp-hidden')) {
        document.getElementById('chat-main-area').style.marginLeft = '0';
      } else {
        document.getElementById('chat-main-area').style.marginLeft = '';
      }
    }
  });

  // ── Init ──────────────────────────────────────────────────────
  (function init() {
    var store = loadStore();
    if (!store.activeId || !store.conversations[store.activeId]) {
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
