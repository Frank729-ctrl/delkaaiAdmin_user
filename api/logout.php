<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/api.php';

$token = get_session_token();
if ($token) {
    try {
        $api = new DelkaiAPI(DELKAI_API_URL);
        $api->logout($token);
    } catch (RuntimeException $e) {
        // Ignore errors — still clear cookie
    }
    clear_session_token();
}

header('Location: /login.php');
exit;
