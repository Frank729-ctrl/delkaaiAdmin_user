/**
 * DelkaAI Developer Console — Client-side JS
 */

// ── DelkaAI logo spinner (shared across chat + support widget) ─
function delkaSpinnerSvg(prefix) {
  var p = prefix;
  return '<svg class="delka-logo-svg" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:44px;height:44px">'
    + '<defs>'
    + '<filter id="' + p + '-ng" x="-150%" y="-150%" width="400%" height="400%">'
    +   '<feGaussianBlur stdDeviation="5" result="b1"/>'
    +   '<feGaussianBlur stdDeviation="11" result="b2"/>'
    +   '<feMerge><feMergeNode in="b2"/><feMergeNode in="b1"/><feMergeNode in="SourceGraphic"/></feMerge>'
    + '</filter>'
    + '<filter id="' + p + '-sg" x="-100%" y="-100%" width="300%" height="300%">'
    +   '<feGaussianBlur stdDeviation="3" result="b"/>'
    +   '<feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>'
    + '</filter>'
    + '<filter id="' + p + '-og" x="-200%" y="-200%" width="500%" height="500%">'
    +   '<feGaussianBlur stdDeviation="4" result="b"/>'
    +   '<feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>'
    + '</filter>'
    + '<radialGradient id="' + p + '-nd" cx="38%" cy="32%" r="62%">'
    +   '<stop offset="0%" stop-color="#D0CAFF"/>'
    +   '<stop offset="50%" stop-color="#7C6FFF"/>'
    +   '<stop offset="100%" stop-color="#4A3FCC"/>'
    + '</radialGradient>'
    + '<radialGradient id="' + p + '-hz" cx="50%" cy="50%" r="50%">'
    +   '<stop offset="0%" stop-color="#7C6FFF" stop-opacity="1"/>'
    +   '<stop offset="100%" stop-color="#7C6FFF" stop-opacity="0"/>'
    + '</radialGradient>'
    + '<linearGradient id="' + p + '-ar" x1="0%" y1="0%" x2="100%" y2="100%">'
    +   '<stop offset="0%" stop-color="#A89BFF" stop-opacity="0"/>'
    +   '<stop offset="45%" stop-color="#7C6FFF" stop-opacity="1"/>'
    +   '<stop offset="100%" stop-color="#D0CAFF" stop-opacity=".5"/>'
    + '</linearGradient>'
    + '<linearGradient id="' + p + '-sp" x1="0%" y1="0%" x2="0%" y2="100%">'
    +   '<stop offset="0%" stop-color="#A89BFF" stop-opacity="0"/>'
    +   '<stop offset="40%" stop-color="#7C6FFF" stop-opacity="1"/>'
    +   '<stop offset="100%" stop-color="#D0CAFF" stop-opacity=".5"/>'
    + '</linearGradient>'
    + '</defs>'
    + '<line x1="30" y1="12" x2="30" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>'
    + '<line x1="30" y1="12" x2="66" y2="12" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>'
    + '<line x1="30" y1="108" x2="66" y2="108" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>'
    + '<line x1="30" y1="60" x2="52" y2="60" stroke="white" stroke-width="7.5" stroke-linecap="round" opacity="0.12"/>'
    + '<path d="M 66 12 C 124 12 124 108 66 108" stroke="white" stroke-width="7.5" stroke-linecap="round" fill="none" opacity="0.12"/>'
    + '<line x1="30" y1="12" x2="30" y2="108" stroke="url(#' + p + '-sp)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-spine" filter="url(#' + p + '-sg)"/>'
    + '<line x1="30" y1="60" x2="52" y2="60" stroke="url(#' + p + '-ar)" stroke-width="7.5" stroke-linecap="round" class="delka-anim-connector" filter="url(#' + p + '-sg)"/>'
    + '<path d="M 66 12 C 124 12 124 108 66 108" stroke="url(#' + p + '-ar)" stroke-width="7.5" stroke-linecap="round" fill="none" class="delka-anim-arc" filter="url(#' + p + '-sg)"/>'
    + '<circle cx="60" cy="60" r="24" stroke="#7C6FFF" stroke-width="0.6" fill="none" opacity="0.07"/>'
    + '<g class="delka-anim-orbit" filter="url(#' + p + '-og)">'
    +   '<circle cx="60" cy="60" r="4.5" fill="#A89BFF"/>'
    +   '<circle cx="60" cy="60" r="2" fill="white"/>'
    + '</g>'
    + '<circle cx="60" cy="60" r="20" fill="url(#' + p + '-hz)" class="delka-anim-haze1"/>'
    + '<circle cx="60" cy="60" r="13" fill="url(#' + p + '-hz)" class="delka-anim-haze2"/>'
    + '<g class="delka-anim-breathe" filter="url(#' + p + '-ng)">'
    +   '<circle cx="60" cy="60" r="9" fill="url(#' + p + '-nd)"/>'
    +   '<circle cx="56" cy="56" r="3" fill="white" opacity="0.4"/>'
    + '</g>'
    + '</svg>';
}

// ── Navigation loading bar ────────────────────────────────────
(function () {
  var bar = document.createElement('div');
  bar.id = 'nav-progress';
  bar.style.cssText = [
    'position:fixed', 'top:0', 'left:0', 'width:0', 'height:3px',
    'background:var(--accent,#7c3aed)', 'z-index:9999',
    'transition:width 0.2s ease', 'pointer-events:none',
  ].join(';');
  document.documentElement.appendChild(bar);

  function startProgress() {
    bar.style.transition = 'none';
    bar.style.width = '0';
    requestAnimationFrame(function () {
      bar.style.transition = 'width 15s cubic-bezier(0.1,0.05,0,1)';
      bar.style.width = '92%';
    });
  }

  function finishProgress() {
    bar.style.transition = 'width 0.15s ease';
    bar.style.width = '100%';
    setTimeout(function () {
      bar.style.opacity = '0';
      setTimeout(function () { bar.style.width = '0'; bar.style.opacity = '1'; }, 200);
    }, 150);
  }

  // Trigger on any link click (not same-page anchors, not new tabs)
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href]');
    if (!a || a.target === '_blank' || a.href.startsWith('mailto:') ||
        a.href.startsWith('javascript:') || a.getAttribute('href').startsWith('#')) return;
    startProgress();
  });

  // Trigger on form submit
  document.addEventListener('submit', function () { startProgress(); });

  // Finish when page fully loads
  window.addEventListener('pageshow', finishProgress);
  window.addEventListener('load', finishProgress);
}());

// ── Copy to clipboard ─────────────────────────────────────────
function copyToClipboard(text, btn) {
  if (!text) return;

  navigator.clipboard.writeText(text).then(function () {
    if (btn) {
      var original = btn.textContent;
      btn.textContent = 'Copied!';
      btn.classList.add('copied');
      setTimeout(function () {
        btn.textContent = original;
        btn.classList.remove('copied');
      }, 2000);
    }
  }).catch(function () {
    // Fallback for older browsers
    var el = document.createElement('textarea');
    el.value = text;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);

    if (btn) {
      var original = btn.textContent;
      btn.textContent = 'Copied!';
      btn.classList.add('copied');
      setTimeout(function () {
        btn.textContent = original;
        btn.classList.remove('copied');
      }, 2000);
    }
  });
}

// Attach copy handlers to all .copy-btn elements
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.getAttribute('data-copy');
      if (!target) {
        // Try to find sibling code element
        var code = btn.parentElement.querySelector('code');
        if (code) target = code.textContent;
      }
      copyToClipboard(target, btn);
    });
  });
});

// ── Toggle password visibility ────────────────────────────────
function togglePassword(inputId, btn) {
  var input = document.getElementById(inputId);
  if (!input) return;

  if (input.type === 'password') {
    input.type = 'text';
    if (btn) btn.innerHTML = eyeOffSVG();
  } else {
    input.type = 'password';
    if (btn) btn.innerHTML = eyeSVG();
  }
}

function eyeSVG() {
  return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
}

function eyeOffSVG() {
  return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
}

// ── Auto-dismiss alerts ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.5s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 4000);
  });
});

// ── Confirm dialogs for destructive actions ───────────────────
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      var msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    });
  });

  // Also handle forms with data-confirm
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) {
        e.preventDefault();
        return false;
      }
    });
  });
});

// ── Playground: dynamic form fields based on endpoint ────────
document.addEventListener('DOMContentLoaded', function () {
  var endpointSelect = document.getElementById('playground-endpoint');
  if (!endpointSelect) return;

  endpointSelect.addEventListener('change', function () {
    updatePlaygroundForm(this.value);
  });

  // Initialize on load
  updatePlaygroundForm(endpointSelect.value);
});

function updatePlaygroundForm(endpoint) {
  var container = document.getElementById('playground-fields');
  if (!container) return;

  var fields = {
    cv: [
      { name: 'raw_text', label: 'Applicant Background', type: 'textarea', placeholder: 'Jane Doe, Software Engineer. 5 years Python, FastAPI, Docker. BSc Computer Science, University of Ghana 2018...' },
    ],
    cover_letter: [
      { name: 'applicant_name',       label: 'Your Name',       type: 'text',     placeholder: 'Jane Doe' },
      { name: 'company_name',         label: 'Company Name',    type: 'text',     placeholder: 'Acme Corp' },
      { name: 'job_title',            label: 'Job Title',       type: 'text',     placeholder: 'Senior Backend Engineer' },
      { name: 'job_description',      label: 'Job Description', type: 'textarea', placeholder: 'We are looking for a motivated engineer...' },
      { name: 'applicant_background', label: 'Your Background', type: 'textarea', placeholder: '5 years Python, FastAPI, Docker...' },
    ],
    chat: [
      { name: 'message',    label: 'Message',    type: 'textarea', placeholder: 'Ask anything...' },
      { name: 'session_id', label: 'Session ID', type: 'text',     placeholder: 'user-session-123 (optional, leave blank to auto-generate)' },
    ],
    vision: [
      { name: 'image_url', label: 'Image URL', type: 'text', placeholder: 'https://example.com/photo.jpg' },
    ],
    feedback: [
      { name: 'session_id', label: 'Session ID',    type: 'text',     placeholder: 'The session ID you want to rate' },
      { name: 'service',    label: 'Service',        type: 'select',   options: ['cv', 'cover_letter', 'chat', 'vision'] },
      { name: 'rating',     label: 'Rating (1–5)',   type: 'number',   placeholder: '5' },
      { name: 'comment',    label: 'Comment',        type: 'textarea', placeholder: 'Optional feedback...' },
    ],
  };

  var selected = fields[endpoint] || fields['cv'];

  container.innerHTML = selected.map(function (f) {
    var input;
    if (f.type === 'textarea') {
      input = '<textarea name="' + f.name + '" placeholder="' + (f.placeholder || '') + '"></textarea>';
    } else if (f.type === 'select') {
      input = '<select name="' + f.name + '">' + (f.options || []).map(function(o) {
        return '<option value="' + o + '">' + o + '</option>';
      }).join('') + '</select>';
    } else {
      input = '<input type="' + f.type + '" name="' + f.name + '" placeholder="' + (f.placeholder || '') + '">';
    }
    return '<div class="form-group"><label>' + f.label + '</label>' + input + '</div>';
  }).join('');
}

// ── Floating support chat widget ─────────────────────────────
(function () {
  var SESSION_KEY = 'delkai_support_session';

  function getSession() {
    return localStorage.getItem(SESSION_KEY) || ('console-' + Math.random().toString(36).slice(2));
  }

  function saveSession(id) {
    localStorage.setItem(SESSION_KEY, id);
  }

  function appendMessage(list, role, text) {
    var div = document.createElement('div');
    div.className = 'sc-msg sc-msg-' + role;
    div.textContent = text;
    list.appendChild(div);
    list.scrollTop = list.scrollHeight;
    return div;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var btn   = document.getElementById('support-chat-btn');
    var panel = document.getElementById('support-chat-panel');
    var close = document.getElementById('support-chat-close');
    var form  = document.getElementById('support-chat-form');
    var input = document.getElementById('support-chat-input');
    var list  = document.getElementById('support-chat-messages');
    if (!btn || !panel) return;

    var session = getSession();
    saveSession(session);

    btn.addEventListener('click', function () {
      var open = panel.classList.toggle('sc-open');
      if (open && list.children.length === 0) {
        appendMessage(list, 'assistant', 'Hi! I\'m the DelkaAI support assistant. Ask me anything about the API, endpoints, or your integration.');
      }
      if (open) setTimeout(function () { input.focus(); }, 100);
    });

    close.addEventListener('click', function () {
      panel.classList.remove('sc-open');
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = input.value.trim();
      if (!msg) return;
      input.value = '';
      appendMessage(list, 'user', msg);

      var thinking = document.createElement('div');
      thinking.className = 'sc-msg sc-msg-assistant sc-thinking';
      thinking.innerHTML = delkaSpinnerSvg('sw');
      list.appendChild(thinking);
      list.scrollTop = list.scrollHeight;
      var sendBtn = form.querySelector('button');
      sendBtn.disabled = true;

      fetch('/support-chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: msg, session_id: session }),
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        thinking.remove();
        if (data.error) {
          appendMessage(list, 'assistant sc-error', 'Error: ' + data.error);
        } else {
          appendMessage(list, 'assistant', data.reply || '(no reply)');
          if (data.session_id) { session = data.session_id; saveSession(session); }
        }
      })
      .catch(function (err) {
        thinking.remove();
        appendMessage(list, 'assistant sc-error', 'Connection error: ' + err.message);
      })
      .finally(function () { sendBtn.disabled = false; });
    });
  });
}());

// ── Mobile sidebar toggle ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  var toggleBtn = document.getElementById('sidebar-toggle');
  var sidebar   = document.getElementById('sidebar');
  var overlay   = document.getElementById('sidebar-overlay');
  if (!toggleBtn || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
  }

  toggleBtn.addEventListener('click', function () {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });
  overlay.addEventListener('click', closeSidebar);

  // Close sidebar when a nav link is clicked on mobile
  sidebar.querySelectorAll('.nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });
});

// ── Playground: send request ──────────────────────────────────
var playgroundForm = document.getElementById('playground-form');
if (playgroundForm) {
  playgroundForm.addEventListener('submit', function (e) {
    e.preventDefault();

    var responseArea = document.getElementById('playground-response');
    var btn = playgroundForm.querySelector('[type=submit]');

    if (responseArea) {
      responseArea.textContent = 'Sending request...';
    }
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Sending...';
    }

    var formData = new FormData(playgroundForm);

    fetch(window.location.href, {
      method: 'POST',
      body: formData,
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (responseArea) {
        responseArea.textContent = JSON.stringify(data, null, 2);
      }
    })
    .catch(function (err) {
      if (responseArea) {
        responseArea.textContent = 'Error: ' + err.message;
      }
    })
    .finally(function () {
      if (btn) {
        btn.disabled = false;
        btn.textContent = 'Send Request';
      }
    });
  });
}
