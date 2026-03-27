<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

clear_auth_cookie();
header('Location: /login');
exit;
