<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

clear_admin_session();
header('Location: /admin/login.php');
exit;
