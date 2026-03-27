<?php
/**
 * DelkaAI Developer Console — Configuration
 */

defined('DELKAI_API_URL')    || define('DELKAI_API_URL',    getenv('DELKAI_API_URL')    ?: 'https://delka.onrender.com');
defined('DELKAI_MASTER_KEY') || define('DELKAI_MASTER_KEY', getenv('DELKAI_MASTER_KEY') ?: '');
defined('ADMIN_EMAIL')       || define('ADMIN_EMAIL',       getenv('ADMIN_EMAIL')       ?: '');
defined('ADMIN_PASSWORD')    || define('ADMIN_PASSWORD',    getenv('ADMIN_PASSWORD')    ?: '');

// Clerk auth
defined('CLERK_PUBLISHABLE_KEY') || define('CLERK_PUBLISHABLE_KEY', getenv('CLERK_PUBLISHABLE_KEY') ?: '');
defined('CLERK_SECRET_KEY')      || define('CLERK_SECRET_KEY',      getenv('CLERK_SECRET_KEY')      ?: '');
defined('CLERK_FRONTEND_URL')    || define('CLERK_FRONTEND_URL',    getenv('CLERK_FRONTEND_URL')    ?: 'https://special-troll-34.clerk.accounts.dev');

defined('RESEND_API_KEY')    || define('RESEND_API_KEY',    getenv('RESEND_API_KEY')    ?: '');
defined('RESEND_FROM_EMAIL') || define('RESEND_FROM_EMAIL', getenv('RESEND_FROM_EMAIL') ?: 'support@snafrate.com');

defined('SESSION_COOKIE') || define('SESSION_COOKIE', 'delkai_dev_session');
defined('ADMIN_COOKIE')   || define('ADMIN_COOKIE',   'delkai_admin_session');
