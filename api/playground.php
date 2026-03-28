<?php
/**
 * API Playground.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

require_auth();

$api   = new DelkaiAPI(DELKAI_API_URL);
$user  = get_auth_user();
$email = $user['sub'] ?? '';

// Fetch user keys so they can pick one to test with
$user_keys = [];
try {
    $all_keys  = $api->developerKeys($email);
    $user_keys = array_filter($all_keys, fn($k) => $k['is_active'] ?? false);
} catch (RuntimeException $e) {
    // Non-fatal
}

$response_json = null;
$error         = null;

// Handle AJAX / form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $endpoint = $_POST['endpoint'] ?? 'cv';
    $api_key  = trim($_POST['api_key'] ?? '');

    if (!$api_key) {
        echo json_encode(['error' => 'API key is required.']);
        exit;
    }

    $method  = 'POST';
    $payload = [];

    switch ($endpoint) {
        case 'cv':
            $payload = ['raw_text' => $_POST['raw_text'] ?? '', 'platform' => 'playground'];
            $path    = '/v1/cv/generate';
            break;

        case 'cover_letter':
            $payload = [
                'applicant_name'       => $_POST['applicant_name']       ?? '',
                'company_name'         => $_POST['company_name']         ?? '',
                'job_title'            => $_POST['job_title']            ?? '',
                'job_description'      => $_POST['job_description']      ?? '',
                'applicant_background' => $_POST['applicant_background'] ?? '',
                'platform'             => 'playground',
            ];
            $path = '/v1/cover-letter/generate';
            break;

        case 'chat':
            $payload = [
                'message'    => $_POST['message']    ?? '',
                'user_id'    => 'playground-user',
                'session_id' => $_POST['session_id'] ?? ('pg-' . md5(session_id() ?: uniqid())),
                'platform'   => 'playground',
            ];
            $path = '/v1/chat';
            break;

        case 'vision':
            $payload = [
                'platform'  => 'playground',
                'image_url' => $_POST['image_url'] ?? '',
            ];
            $path = '/v1/vision/search';
            break;

        case 'feedback':
            $payload = [
                'session_id' => $_POST['session_id'] ?? 'playground-session',
                'service'    => $_POST['service']    ?? 'cv',
                'rating'     => (int)($_POST['rating'] ?? 5),
                'comment'    => $_POST['comment']    ?? '',
            ];
            $path = '/v1/feedback';
            break;

        default:
            echo json_encode(['error' => 'Unknown endpoint.']);
            exit;
    }

    $ch = curl_init(rtrim(DELKAI_API_URL, '/') . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 45,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-DelkaAI-Key: ' . $api_key,
        ],
    ]);
    $raw      = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['error' => 'cURL error: ' . $err]);
        exit;
    }

    // Parse SSE stream for streaming endpoints
    if (str_contains($raw ?? '', 'data: ')) {
        $text = '';
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'data: ')) {
                $token = substr($line, 6);
                if ($token !== '[DONE]') $text .= $token;
            }
        }
        $decoded = ['reply' => $text, '_streamed' => true];
    } else {
        $decoded = json_decode($raw, true) ?? ['raw' => $raw];
    }

    $decoded['_http_status'] = $httpCode;
    echo json_encode($decoded, JSON_PRETTY_PRINT);
    exit;
}

$active_page = 'playground';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Playground — DelkaAI Console</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-header">
      <div class="page-header-left">
        <h1>API Playground</h1>
        <p>Test the DelkaAI API endpoints interactively.</p>
      </div>
    </div>

    <div class="playground-layout">
      <!-- Left: Request builder -->
      <div>
        <div class="card">
          <div class="card-header"><h2>Request Builder</h2></div>
          <div class="card-body">
            <form id="playground-form">
              <div class="form-group">
                <label for="playground-endpoint">Endpoint</label>
                <select id="playground-endpoint" name="endpoint">
                  <option value="cv">CV Generation — POST /v1/cv/generate</option>
                  <option value="cover_letter">Cover Letter — POST /v1/cover-letter/generate</option>
                  <option value="chat">AI Chat — POST /v1/chat</option>
                  <option value="vision">Visual Search — POST /v1/vision/search</option>
                  <option value="feedback">Feedback — POST /v1/feedback</option>
                </select>
              </div>

              <div class="form-group">
                <label for="api_key_input">API Key</label>
                <?php if (!empty($user_keys)): ?>
                <select name="api_key" id="api_key_input">
                  <?php foreach ($user_keys as $k): ?>
                  <option value="">— select a key —</option>
                  <?php endforeach; ?>
                </select>
                <p class="form-hint">Or paste a key manually below:</p>
                <input type="text" name="api_key_manual" placeholder="pk_live_..." style="margin-top:6px;" id="api_key_manual">
                <?php else: ?>
                <input type="text" name="api_key" id="api_key_input" placeholder="pk_live_..." required>
                <p class="form-hint">Paste your API key here.</p>
                <?php endif; ?>
              </div>

              <hr class="divider">

              <div id="playground-fields">
                <!-- Dynamically populated by app.js -->
              </div>

              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;">
                Send Request
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Right: Response -->
      <div>
        <div class="card" style="height:100%;">
          <div class="card-header">
            <h2>Response</h2>
            <span id="response-status" class="badge badge-neutral" style="display:none;"></span>
          </div>
          <div class="card-body" style="padding:0;">
            <pre class="response-area" id="playground-response" style="border:none;border-radius:0 0 8px 8px;margin:0;min-height:420px;">Waiting for request...</pre>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="/js/app.js"></script>
<script>
// Override the default playground submit to handle api_key_manual
document.getElementById('playground-form').addEventListener('submit', function(e) {
  e.preventDefault();

  var responseArea = document.getElementById('playground-response');
  var statusBadge  = document.getElementById('response-status');
  var btn          = this.querySelector('[type=submit]');

  // Merge manual key if provided
  var manualInput = document.getElementById('api_key_manual');
  if (manualInput && manualInput.value.trim()) {
    var selectInput = document.getElementById('api_key_input');
    if (selectInput && !selectInput.value) {
      selectInput.name = '';
      manualInput.name = 'api_key';
    }
  }

  responseArea.textContent = 'Sending request...';
  statusBadge.style.display = 'none';
  btn.disabled = true;
  btn.textContent = 'Sending...';

  var formData = new FormData(this);

  fetch(window.location.href, {
    method: 'POST',
    body: formData,
  })
  .then(function(res) { return res.json(); })
  .then(function(data) {
    var status = data._http_status;
    if (status) {
      statusBadge.style.display = 'inline-flex';
      statusBadge.textContent   = 'HTTP ' + status;
      statusBadge.className     = 'badge ' + (status < 300 ? 'badge-success' : (status < 500 ? 'badge-warning' : 'badge-error'));
    }
    responseArea.textContent = JSON.stringify(data, null, 2);
  })
  .catch(function(err) {
    responseArea.textContent = 'Error: ' + err.message;
  })
  .finally(function() {
    btn.disabled = false;
    btn.textContent = 'Send Request';
  });
}, true); // capture phase to replace default handler
</script>
</body>
</html>
