<?php
/**
 * Clerk JWT verification and user info helper.
 * Verifies the __session cookie issued by Clerk's JS SDK.
 */

function _base64url_decode(string $data): string {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Verify a Clerk session JWT using the JWKS public key.
 * Returns the decoded payload array or null on failure.
 */
function clerk_verify_jwt(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header_b64, $payload_b64, $sig_b64] = $parts;

    $payload = json_decode(_base64url_decode($payload_b64), true);
    if (!$payload) return null;

    // Check expiry
    if (($payload['exp'] ?? 0) < time()) return null;

    // Verify signature with Clerk's public key
    $public_key_pem = "-----BEGIN PUBLIC KEY-----\n" .
        "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsviTSSvW1rc6aLLyBxUt\n" .
        "n6C4oT3XgKYsGqWfZEdYGf9Yai8Efc/hw3YMx1PiOw1lhGdXTgT02aEocrrEOI9g\n" .
        "hbd/yCdeM+Mr+SHi5FWWeVfUiURRG/mXQigCUuYaXp1ZdtaKE+7LriMF98rQb3TP\n" .
        "8cmzVoDJmzcKGEivU06ZNB5fL6u9NqZHJyxIDvCSnOZQwbGbpPGfcbao1m6t4ymX\n" .
        "cE4csYu8Vc/DzGvEeH4007uUGkFOA0JA3Nsp9h6mWmVlZTMvdBztPb5YYp3bVsFX\n" .
        "Bdqv4E9lcOLgOLxKKbnvSpplVSlXczGEhibJJ9u9HsB5nL7qGUuMd2bgBCqWaisy\n" .
        "dwIDAQAB\n" .
        "-----END PUBLIC KEY-----";

    $pub = openssl_pkey_get_public($public_key_pem);
    if (!$pub) return null;

    $sig  = _base64url_decode($sig_b64);
    $data = $header_b64 . '.' . $payload_b64;

    if (openssl_verify($data, $sig, $pub, OPENSSL_ALGO_SHA256) !== 1) return null;

    return $payload;
}

/**
 * Fetch full user info from Clerk's API using the user ID (sub claim).
 * Returns array with id, email, first_name, last_name or null on failure.
 */
function clerk_get_user(string $user_id): ?array {
    $ch = curl_init('https://api.clerk.com/v1/users/' . urlencode($user_id));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
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
 * Returns user array or null if not authenticated.
 * Caches user info in a short-lived cookie to avoid repeated API calls.
 */
function clerk_current_user(): ?array {
    $session_token = $_COOKIE['__session'] ?? null;
    if (!$session_token) return null;

    $payload = clerk_verify_jwt($session_token);
    if (!$payload) return null;

    $user_id = $payload['sub'] ?? null;
    if (!$user_id) return null;

    // Use cached user info if available and matches current user
    $cached_id    = $_COOKIE['_clerk_uid']   ?? null;
    $cached_email = $_COOKIE['_clerk_email'] ?? null;
    $cached_name  = $_COOKIE['_clerk_name']  ?? null;

    if ($cached_id === $user_id && $cached_email) {
        return ['id' => $user_id, 'email' => $cached_email, 'full_name' => $cached_name];
    }

    // Fetch from Clerk API
    $user = clerk_get_user($user_id);
    if (!$user) return null;

    // Cache for 1 hour
    setcookie('_clerk_uid',   $user['id'],        time() + 3600, '/', '', true, true);
    setcookie('_clerk_email', $user['email'],      time() + 3600, '/', '', true, true);
    setcookie('_clerk_name',  $user['full_name'],  time() + 3600, '/', '', true, true);

    return $user;
}
