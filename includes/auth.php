<?php
/**
 * Session / auth helpers for the DelkaAI developer console.
 */

require_once __DIR__ . '/../config.php';

function get_session_token(): ?string
{
    return $_COOKIE[SESSION_COOKIE] ?? null;
}

function set_session_token(string $token, ?string $expires = null): void
{
    $expiry = $expires ? strtotime($expires) : (time() + 86400 * 7);
    setcookie(SESSION_COOKIE, $token, [
        'expires'  => $expiry,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_session_token(): void
{
    setcookie(SESSION_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function get_admin_session(): ?string
{
    return $_COOKIE[ADMIN_COOKIE] ?? null;
}

function set_admin_session(): void
{
    setcookie(ADMIN_COOKIE, 'delkai_admin_ok', [
        'expires'  => time() + 28800, // 8 hours
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_admin_session(): void
{
    setcookie(ADMIN_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function is_admin(): bool
{
    return get_admin_session() === 'delkai_admin_ok';
}

function require_auth(): void
{
    if (!get_session_token()) {
        header('Location: /login.php');
        exit;
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}
