<?php
/**
 * DelkaAI API client.
 */
class DelkaiAPI
{
    private string $baseUrl;

    public function __construct(string $base_url)
    {
        $this->baseUrl = rtrim($base_url, '/');
    }

    /**
     * Make an HTTP request.
     *
     * @param  string $method   GET | POST | DELETE
     * @param  string $path     e.g. /v1/developer/me
     * @param  array  $data     Body data (JSON-encoded for POST)
     * @param  array  $headers  Extra headers
     * @return array            Decoded JSON response
     * @throws RuntimeException on HTTP error
     */
    private function request(string $method, string $path, array $data = [], array $headers = []): array
    {
        $url = $this->baseUrl . $path;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $defaultHeaders = ['Content-Type: application/json', 'Accept: application/json'];
        $allHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            throw new RuntimeException('cURL error: ' . $curlErr);
        }

        $decoded = json_decode($body, true) ?? [];

        if ($httpCode >= 400) {
            $detail = $decoded['detail'] ?? $decoded['message'] ?? 'API error';
            throw new RuntimeException($detail, $httpCode);
        }

        return $decoded;
    }

    // ── Developer auth ───────────────────────────────────────────────────────

    public function register(string $email, string $password, string $full_name, ?string $company = null): array
    {
        $data = ['email' => $email, 'password' => $password, 'full_name' => $full_name];
        if ($company !== null && $company !== '') {
            $data['company'] = $company;
        }
        return $this->request('POST', '/v1/developer/register', $data);
    }

    /** Returns session_token string or null on failure. */
    public function login(string $email, string $password): ?string
    {
        try {
            $res = $this->request('POST', '/v1/developer/login', [
                'email'    => $email,
                'password' => $password,
            ]);
            return $res['session_token'] ?? null;
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /** Returns full login response array (including expires_at) or null on failure. */
    public function loginFull(string $email, string $password): ?array
    {
        try {
            return $this->request('POST', '/v1/developer/login', [
                'email'    => $email,
                'password' => $password,
            ]);
        } catch (RuntimeException $e) {
            return null;
        }
    }

    public function logout(string $session_token): array
    {
        return $this->request('POST', '/v1/developer/logout', [], [
            'X-Delkai-Session: ' . $session_token,
        ]);
    }

    public function me(string $session_token): array
    {
        return $this->request('GET', '/v1/developer/me', [], [
            'X-Delkai-Session: ' . $session_token,
        ]);
    }

    public function overview(string $session_token): array
    {
        return $this->request('GET', '/v1/developer/overview', [], [
            'X-Delkai-Session: ' . $session_token,
        ]);
    }

    public function createDeveloperKey(string $session_token, string $key_name): array
    {
        return $this->request('POST', '/v1/developer/keys/create', [
            'key_name' => $key_name,
        ], ['X-Delkai-Session: ' . $session_token]);
    }

    public function revokeDeveloperKey(string $session_token, string $key_prefix): array
    {
        return $this->request('POST', '/v1/developer/keys/revoke', [
            'key_prefix' => $key_prefix,
        ], ['X-Delkai-Session: ' . $session_token]);
    }

    public function keys(string $session_token): array
    {
        return $this->request('GET', '/v1/developer/keys', [], [
            'X-Delkai-Session: ' . $session_token,
        ]);
    }

    /**
     * Exchange a verified Clerk identity for a DelkaAI session token.
     * Returns array with session_token + expires_at, or null on failure.
     */
    public function clerkProvision(string $email, string $full_name, string $clerk_id, string $master_key): ?array
    {
        try {
            return $this->request('POST', '/v1/developer/clerk-provision', [
                'email'     => $email,
                'full_name' => $full_name,
                'clerk_id'  => $clerk_id,
            ], ['X-DelkaAI-Master-Key: ' . $master_key]);
        } catch (RuntimeException $e) {
            return null;
        }
    }

    // ── Admin ────────────────────────────────────────────────────────────────

    private function masterHeader(string $master_key): string
    {
        return 'X-DelkaAI-Master-Key: ' . $master_key;
    }

    public function adminKeys(string $master_key): array
    {
        return $this->request('GET', '/v1/admin/keys/list', [], [
            $this->masterHeader($master_key),
        ]);
    }

    public function adminCreateKey(string $master_key, string $platform, string $owner, bool $requires_hmac = false): array
    {
        return $this->request('POST', '/v1/admin/keys/create', [
            'platform'      => $platform,
            'owner'         => $owner,
            'requires_hmac' => $requires_hmac,
        ], [
            $this->masterHeader($master_key),
        ]);
    }

    public function adminRevokeKey(string $master_key, string $key_prefix): array
    {
        return $this->request('POST', '/v1/admin/keys/revoke', [
            'key_prefix' => $key_prefix,
        ], [
            $this->masterHeader($master_key),
        ]);
    }

    public function adminMetrics(string $master_key): array
    {
        return $this->request('GET', '/v1/admin/metrics', [], [
            $this->masterHeader($master_key),
        ]);
    }

    public function adminBlockedIps(string $master_key): array
    {
        return $this->request('GET', '/v1/admin/blocked-ips', [], [
            $this->masterHeader($master_key),
        ]);
    }

    public function adminUnblockIp(string $master_key, string $ip_address): array
    {
        return $this->request('POST', '/v1/admin/unblock-ip', [
            'ip_address' => $ip_address,
        ], [
            $this->masterHeader($master_key),
        ]);
    }

    public function health(): array
    {
        return $this->request('GET', '/v1/health');
    }
}
