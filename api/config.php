<?php
/**
 * DelkaAI Developer Console — Configuration
 */

// Vercel PHP may expose env vars via $_ENV, $_SERVER, or getenv() depending on runtime.
function _env(string $key, string $default = ''): string {
    $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($v !== false && $v !== '') ? (string)$v : $default;
}

defined('DELKAI_API_URL')    || define('DELKAI_API_URL',    _env('DELKAI_API_URL',    'https://delka.onrender.com'));
defined('DELKAI_MASTER_KEY') || define('DELKAI_MASTER_KEY', _env('DELKAI_MASTER_KEY', ''));
defined('ADMIN_EMAIL')       || define('ADMIN_EMAIL',       _env('ADMIN_EMAIL',       ''));
defined('ADMIN_PASSWORD')    || define('ADMIN_PASSWORD',    _env('ADMIN_PASSWORD',    ''));

defined('RESEND_API_KEY')    || define('RESEND_API_KEY',    _env('RESEND_API_KEY',    ''));
defined('RESEND_FROM_EMAIL') || define('RESEND_FROM_EMAIL', _env('RESEND_FROM_EMAIL', 'support@snafrate.com'));

defined('JWT_SECRET') || define('JWT_SECRET', _env('JWT_SECRET', '22bc88187dfa01e6ee28550d402f04aea6b75d7cf9acc4234770e73b2cee5339'));

defined('DB_DSN')  || define('DB_DSN',  'pgsql:host=aws-1-eu-west-1.pooler.supabase.com;port=5432;dbname=postgres;sslmode=require');
defined('DB_USER') || define('DB_USER', 'postgres.vtnfqdqbuonszfdhglyq');
defined('DB_PASS') || define('DB_PASS', 'ZkeoQBQsPbKzMfca');

defined('SESSION_COOKIE') || define('SESSION_COOKIE', 'delkai_dev_session');
defined('ADMIN_COOKIE')   || define('ADMIN_COOKIE',   'delkai_admin_session');
