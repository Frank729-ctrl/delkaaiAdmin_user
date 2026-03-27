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

defined('SUPABASE_URL')         || define('SUPABASE_URL',         _env('SUPABASE_URL',         ''));
defined('SUPABASE_SERVICE_KEY') || define('SUPABASE_SERVICE_KEY', _env('SUPABASE_SERVICE_KEY', ''));
defined('JWT_SECRET')           || define('JWT_SECRET',           _env('JWT_SECRET',           'change-me-in-vercel-env'));

defined('SESSION_COOKIE') || define('SESSION_COOKIE', 'delkai_dev_session');
defined('ADMIN_COOKIE')   || define('ADMIN_COOKIE',   'delkai_admin_session');
