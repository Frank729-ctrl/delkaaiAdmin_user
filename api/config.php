<?php
/**
 * DelkaAI Developer Console — Configuration
 */

defined('DELKAI_API_URL')    || define('DELKAI_API_URL',    getenv('DELKAI_API_URL')    ?: 'https://delka.onrender.com');
defined('DELKAI_MASTER_KEY') || define('DELKAI_MASTER_KEY', getenv('DELKAI_MASTER_KEY') ?: '');
defined('ADMIN_EMAIL')       || define('ADMIN_EMAIL',       getenv('ADMIN_EMAIL')       ?: '');
defined('ADMIN_PASSWORD')    || define('ADMIN_PASSWORD',    getenv('ADMIN_PASSWORD')    ?: '');


defined('RESEND_API_KEY')    || define('RESEND_API_KEY',    getenv('RESEND_API_KEY')    ?: '');
defined('RESEND_FROM_EMAIL') || define('RESEND_FROM_EMAIL', getenv('RESEND_FROM_EMAIL') ?: 'support@snafrate.com');

defined('SUPABASE_URL')         || define('SUPABASE_URL',         getenv('SUPABASE_URL')         ?: '');
defined('SUPABASE_SERVICE_KEY') || define('SUPABASE_SERVICE_KEY', getenv('SUPABASE_SERVICE_KEY') ?: '');
defined('JWT_SECRET')           || define('JWT_SECRET',           getenv('JWT_SECRET')           ?: 'change-me-in-vercel-env');

defined('SESSION_COOKIE') || define('SESSION_COOKIE', 'delkai_dev_session');
defined('ADMIN_COOKIE')   || define('ADMIN_COOKIE',   'delkai_admin_session');
