<?php
/**
 * JWT-based session helpers for the DelkaAI developer console.
 * Sessions are stateless JWTs signed with JWT_SECRET — no backend call needed.
 */
require_once __DIR__ . '/../config.php';

// ── JWT helpers ───────────────────────────────────────────────────────────────

function _b64u(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function _b64d(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_issue(string $email, string $full_name, ?string $company): string
{
    $h = _b64u(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $p = _b64u(json_encode([
        'sub'     => strtolower($email),
        'name'    => $full_name,
        'company' => $company,
        'iat'     => time(),
        'exp'     => time() + 86400 * 7,
    ]));
    $s = _b64u(hash_hmac('sha256', "$h.$p", JWT_SECRET, true));
    return "$h.$p.$s";
}

function jwt_decode(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$h, $p, $s] = $parts;

    $expected = _b64u(hash_hmac('sha256', "$h.$p", JWT_SECRET, true));
    if (!hash_equals($expected, $s)) return null;

    $payload = json_decode(_b64d($p), true);
    if (!$payload || ($payload['exp'] ?? 0) < time()) return null;

    return $payload;
}

// ── Cookie helpers ────────────────────────────────────────────────────────────

function get_auth_user(): ?array
{
    $raw = $_COOKIE[SESSION_COOKIE] ?? null;
    if (!$raw) return null;
    return jwt_decode($raw);
}

function set_auth_cookie(string $email, string $full_name, ?string $company): void
{
    $token = jwt_issue($email, $full_name, $company);
    setcookie(SESSION_COOKIE, $token, [
        'expires'  => time() + 86400 * 7,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax',
    ]);
}

function clear_auth_cookie(): void
{
    setcookie(SESSION_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax',
    ]);
}

// Shims so pages that call the old API still compile without changes.
function get_session_token(): ?string   { return get_auth_user()['sub'] ?? null; }
function set_session_token(): void      {}
function clear_session_token(): void    { clear_auth_cookie(); }

// ── Access guards ─────────────────────────────────────────────────────────────

/** Redirects to /login if unauthenticated. Returns decoded JWT payload. */
function require_auth(): array
{
    $user = get_auth_user();
    if (!$user) {
        header('Location: /login');
        exit;
    }
    return $user;
}

// ── Admin session (cookie-based, unchanged) ───────────────────────────────────

function get_admin_session(): ?string
{
    return $_COOKIE[ADMIN_COOKIE] ?? null;
}

function set_admin_session(): void
{
    setcookie(ADMIN_COOKIE, 'delkai_admin_ok', [
        'expires'  => time() + 28800,
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

function is_admin(): bool  { return get_admin_session() === 'delkai_admin_ok'; }

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /admin/login');
        exit;
    }
}
