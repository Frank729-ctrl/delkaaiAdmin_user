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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
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
            $raw    = $decoded['detail'] ?? $decoded['message'] ?? 'API error';
            $detail = is_array($raw) ? json_encode($raw) : (string) $raw;
            throw new RuntimeException($detail, $httpCode);
        }

        return $decoded;
    }

    // ── Developer key management (via dev-keys endpoints in health_router) ────

    public function developerKeys(string $owner_email): array
    {
        $res = $this->request('GET', '/v1/admin/dev-keys/list?owner=' . urlencode($owner_email), [], [
            $this->masterHeader(DELKAI_MASTER_KEY),
        ]);
        return $res['keys'] ?? [];
    }

    public function developerCreateKey(string $owner_email, string $key_name): array
    {
        return $this->request('POST', '/v1/admin/dev-keys/create', [
            'owner'    => $owner_email,
            'key_name' => $key_name,
        ], [$this->masterHeader(DELKAI_MASTER_KEY)]);
    }

    public function developerRevokeKey(string $owner_email, string $key_prefix): array
    {
        return $this->request('POST', '/v1/admin/dev-keys/revoke', [
            'key_prefix' => $key_prefix,
        ], [$this->masterHeader(DELKAI_MASTER_KEY)]);
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
