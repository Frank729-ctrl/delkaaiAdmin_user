<?php
/**
 * Clerk authentication helper.
 *
 * Verifies sessions via Clerk's Backend API — no local JWT crypto needed.
 * The __session cookie is a short-lived JWT (60 s) refreshed by Clerk JS.
 * We extract the session ID from it and confirm with Clerk's REST API.
 */

function _base64url_decode(string $data): string {
    $rem = strlen($data) % 4;
    if ($rem) $data .= str_repeat('=', 4 - $rem);
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Verify a Clerk __session token via the Clerk Backend API.
 * Returns the verified user_id string, or null on failure.
 */
function clerk_verify_session(string $token): ?string {
    // Decode JWT payload (not verifying signature — Clerk API does that)
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    $payload = json_decode(_base64url_decode($parts[1]), true);
    if (!$payload) return null;

    // Fast-fail on obviously expired token (saves an API round-trip)
    if (($payload['exp'] ?? 0) < time()) return null;

    $session_id = $payload['sid'] ?? null;
    if (!$session_id) return null;

    // Verify with Clerk Backend API
    $ch = curl_init("https://api.clerk.com/v1/sessions/{$session_id}/verify");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['token' => $token]),
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . CLERK_SECRET_KEY,
            'Content-Type: application/json',
            'Clerk-API-Version: 2025-11-10',
        ],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) return null;

    $session = json_decode($body, true);
    return $session['user_id'] ?? null;
}

/**
 * Fetch full user info from Clerk's Backend API.
 */
function clerk_get_user(string $user_id): ?array {
    $ch = curl_init('https://api.clerk.com/v1/users/' . urlencode($user_id));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . CLERK_SECRET_KEY,
            'Clerk-API-Version: 2025-11-10',
        ],
    ]);
    $body = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($body, true);
    if (empty($data['id'])) return null;

    $email = $data['email_addresses'][0]['email_address'] ?? '';
    return [
        'id'         => $data['id'],
        'email'      => $email,
        'first_name' => $data['first_name'] ?? '',
        'last_name'  => $data['last_name']  ?? '',
        'full_name'  => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
    ];
}

/**
 * Get the current Clerk user from the __session cookie.
 *
 * On first call: verifies via Clerk API + fetches user info, caches result.
 * On subsequent calls within cache TTL: uses cache, just checks JWT expiry.
 * Returns user array or null if not authenticated.
 */
function clerk_current_user(): ?array {
    $token = $_COOKIE['__session'] ?? null;
    if (!$token) return null;

    $cached_id    = $_COOKIE['_clerk_uid']   ?? null;
    $cached_email = $_COOKIE['_clerk_email'] ?? null;
    $cached_name  = $_COOKIE['_clerk_name']  ?? null;

    // Cache hit: lightweight JWT expiry check only (no API call)
    if ($cached_id && $cached_email) {
        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $payload = json_decode(_base64url_decode($parts[1]), true);
            if ($payload
                && ($payload['exp'] ?? 0) > time()
                && ($payload['sub'] ?? '') === $cached_id
            ) {
                return ['id' => $cached_id, 'email' => $cached_email, 'full_name' => $cached_name ?? ''];
            }
        }
    }

    // Full verification via Clerk Backend API
    $user_id = clerk_verify_session($token);
    if (!$user_id) return null;

    $user = clerk_get_user($user_id);
    if (!$user) return null;

    // Cache for 55 min (Clerk JS refreshes the JWT every 60 s, keeping it alive)
    setcookie('_clerk_uid',   $user['id'],        time() + 3300, '/', '', true, true);
    setcookie('_clerk_email', $user['email'],      time() + 3300, '/', '', true, true);
    setcookie('_clerk_name',  $user['full_name'],  time() + 3300, '/', '', true, true);

    return $user;
}
