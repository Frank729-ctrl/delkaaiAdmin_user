<?php
/**
 * General AI chat proxy for the developer console chat page.
 * Calls /v1/admin/console-chat (master-key auth) and returns JSON.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

if (!get_auth_user()) {
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST required.']);
    exit;
}

$body      = json_decode(file_get_contents('php://input'), true) ?? [];
$message   = trim($body['message']    ?? '');
$session   = trim($body['session_id'] ?? '');
$user      = get_auth_user();
$user_id   = md5($user['sub'] ?? 'console');

if (!$message) {
    echo json_encode(['error' => 'Message is required.']);
    exit;
}

if (!$session) {
    $session = 'console-chat-' . $user_id;
}

$ch = curl_init(rtrim(DELKAI_API_URL, '/') . '/v1/admin/console-chat');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'message'    => $message,
        'session_id' => $session,
        'user_id'    => $user_id,
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-DelkaAI-Master-Key: ' . DELKAI_MASTER_KEY,
    ],
]);

$raw  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['error' => 'Connection error: ' . $err]);
    exit;
}

if ($code >= 400) {
    $body   = json_decode($raw, true) ?? [];
    $detail = $body['detail'] ?? $body['message'] ?? 'API error';
    echo json_encode(['error' => is_array($detail) ? json_encode($detail) : $detail]);
    exit;
}

$decoded = json_decode($raw, true) ?? [];
echo json_encode([
    'reply'      => $decoded['reply']      ?? '',
    'session_id' => $decoded['session_id'] ?? $session,
]);
