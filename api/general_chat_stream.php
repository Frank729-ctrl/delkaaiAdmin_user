<?php
/**
 * Streaming SSE proxy for the AI chat page.
 * Forwards the FastAPI SSE stream directly to the browser.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!get_auth_user()) {
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$body      = json_decode(file_get_contents('php://input'), true) ?? [];
$message   = trim($body['message']    ?? '');
$session   = trim($body['session_id'] ?? '');
$user      = get_auth_user();
$user_id   = md5($user['sub'] ?? 'console');

if (!$message) {
    http_response_code(422);
    exit;
}

if (!$session) {
    $session = 'console-chat-' . $user_id;
}

// Stream SSE headers
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
header('Connection: keep-alive');

// Flush any output buffers
while (ob_get_level()) {
    ob_end_flush();
}
flush();

// Forward stream from FastAPI using curl
$ch = curl_init(rtrim(DELKAI_API_URL, '/') . '/v1/admin/console-chat/stream');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 120,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'message'    => $message,
        'session_id' => $session,
        'user_id'    => $user_id,
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: text/event-stream',
        'X-DelkaAI-Master-Key: ' . DELKAI_MASTER_KEY,
    ],
    CURLOPT_WRITEFUNCTION => function ($ch, $data) {
        echo $data;
        ob_flush();
        flush();
        return strlen($data);
    },
]);

curl_exec($ch);
curl_close($ch);
