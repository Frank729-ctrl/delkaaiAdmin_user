<?php
/**
 * Clerk JWT verification and user info helper.
 * Verifies the __session cookie issued by Clerk's JS SDK.
 *
 * Uses dynamic JWKS fetching so no public key needs to be hardcoded.
 */

function _base64url_decode(string $data): string {
    $rem = strlen($data) % 4;
    if ($rem) $data .= str_repeat('=', 4 - $rem);
    return base64_decode(strtr($data, '-_', '+/'));
}

// ── ASN.1 helpers for JWK → PEM conversion ───────────────────────────────────

function _asn1_len(int $len): string {
    if ($len < 0x80) return chr($len);
    $packed = ltrim(pack('N', $len), "\x00");
    return chr(0x80 | strlen($packed)) . $packed;
}

function _asn1_int(string $bytes): string {
    $bytes = ltrim($bytes, "\x00") ?: "\x00";
    if (ord($bytes[0]) & 0x80) $bytes = "\x00" . $bytes; // ensure positive
    return "\x02" . _asn1_len(strlen($bytes)) . $bytes;
}

/**
 * Convert an RSA JWK (n, e) to PEM SubjectPublicKeyInfo.
 */
function _jwk_to_pem(array $jwk): ?string {
    if (($jwk['kty'] ?? '') !== 'RSA') return null;
    $n = _base64url_decode($jwk['n'] ?? '');
    $e = _base64url_decode($jwk['e'] ?? '');
    if (!$n || !$e) return null;

    $n_int = _asn1_int($n);
    $e_int = _asn1_int($e);

    // RSAPublicKey SEQUENCE { modulus INTEGER, exponent INTEGER }
    $rsa_inner = $n_int . $e_int;
    $rsa_seq   = "\x30" . _asn1_len(strlen($rsa_inner)) . $rsa_inner;

    // AlgorithmIdentifier: OID rsaEncryption + NULL
    $alg = pack('H*', '300d06092a864886f70d0101010500');

    // BIT STRING wrapping RSAPublicKey (0 unused bits)
    $bits = "\x03" . _asn1_len(strlen($rsa_seq) + 1) . "\x00" . $rsa_seq;

    // SubjectPublicKeyInfo SEQUENCE { algorithm, subjectPublicKey }
    $spki = "\x30" . _asn1_len(strlen($alg . $bits)) . $alg . $bits;

    return "-----BEGIN PUBLIC KEY-----\n" .
           chunk_split(base64_encode($spki), 64, "\n") .
           "-----END PUBLIC KEY-----\n";
}

// ── JWKS fetching with 1-hour file cache ─────────────────────────────────────

function _clerk_fetch_jwks(): array {
    $cache = sys_get_temp_dir() . '/clerk_jwks_' . md5(CLERK_FRONTEND_URL) . '.json';

    if (file_exists($cache) && (time() - filemtime($cache)) < 3600) {
        $data = json_decode(file_get_contents($cache), true);
        if (!empty($data)) return $data;
    }

    $ch = curl_init(CLERK_FRONTEND_URL . '/.well-known/jwks.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
    ]);
    $body = curl_exec($ch);
    curl_close($ch);

    $jwks = json_decode($body, true);
    $keys = $jwks['keys'] ?? [];
    if ($keys) {
        @file_put_contents($cache, json_encode($keys));
    }
    return $keys;
}

// ── Public API ────────────────────────────────────────────────────────────────

/**
 * Verify a Clerk session JWT using the JWKS endpoint.
 * Returns the decoded payload array on success, null on failure.
 */
function clerk_verify_jwt(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header_b64, $payload_b64, $sig_b64] = $parts;

    $header  = json_decode(_base64url_decode($header_b64), true);
    $payload = json_decode(_base64url_decode($payload_b64), true);
    if (!$header || !$payload) return null;

    // Check expiry first (cheap, avoids a network call)
    if (($payload['exp'] ?? 0) < time()) return null;

    $kid  = $header['kid'] ?? null;
    $keys = _clerk_fetch_jwks();

    $pub = null;
    foreach ($keys as $jwk) {
        // Match by kid if present; otherwise try first RSA key
        if ($kid !== null && ($jwk['kid'] ?? null) !== $kid) continue;
        $pem = _jwk_to_pem($jwk);
        if ($pem) {
            $pub = openssl_pkey_get_public($pem);
            if ($pub) break;
        }
    }
    if (!$pub) return null;

    $sig  = _base64url_decode($sig_b64);
    $data = $header_b64 . '.' . $payload_b64;

    if (openssl_verify($data, $sig, $pub, OPENSSL_ALGO_SHA256) !== 1) return null;

    return $payload;
}

/**
 * Fetch full user info from Clerk's Backend API.
 * Returns array with id, email, first_name, last_name, full_name — or null.
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
 * Returns user array or null if not authenticated.
 * Caches user info in short-lived cookies to avoid repeated API calls.
 */
function clerk_current_user(): ?array {
    $session_token = $_COOKIE['__session'] ?? null;
    if (!$session_token) return null;

    $payload = clerk_verify_jwt($session_token);
    if (!$payload) return null;

    $user_id = $payload['sub'] ?? null;
    if (!$user_id) return null;

    // Use cached user info if it matches the current Clerk user
    $cached_id    = $_COOKIE['_clerk_uid']   ?? null;
    $cached_email = $_COOKIE['_clerk_email'] ?? null;
    $cached_name  = $_COOKIE['_clerk_name']  ?? null;

    if ($cached_id === $user_id && $cached_email) {
        return ['id' => $user_id, 'email' => $cached_email, 'full_name' => $cached_name ?? ''];
    }

    $user = clerk_get_user($user_id);
    if (!$user) return null;

    setcookie('_clerk_uid',   $user['id'],        time() + 3600, '/', '', true, true);
    setcookie('_clerk_email', $user['email'],      time() + 3600, '/', '', true, true);
    setcookie('_clerk_name',  $user['full_name'],  time() + 3600, '/', '', true, true);

    return $user;
}
