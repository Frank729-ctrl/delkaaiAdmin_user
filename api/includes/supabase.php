<?php
/**
 * Supabase Auth client — uses GoTrue auth API, no direct table access.
 * This avoids the PostgREST grants issue with externally-created tables.
 */
class SupabaseClient
{
    private string $url;
    private string $key;

    public function __construct()
    {
        $this->url = rtrim(SUPABASE_URL, '/');
        $this->key = SUPABASE_SERVICE_KEY;
    }

    private function post(string $path, array $body): array
    {
        $ch = curl_init($this->url . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'apikey: '               . $this->key,
                'Authorization: Bearer ' . $this->key,
            ],
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) throw new RuntimeException('cURL: ' . $err);

        $data = json_decode($raw, true) ?? [];
        return ['code' => $code, 'data' => $data];
    }

    /**
     * Register via Supabase Auth Admin API (service_role, skips email confirmation).
     * Returns null on success, 'duplicate' for existing email, error string otherwise.
     */
    public function register(string $email, string $password, string $full_name, ?string $company): ?string
    {
        $res = $this->post('/auth/v1/admin/users', [
            'email'         => strtolower($email),
            'password'      => $password,
            'email_confirm' => true,
            'user_metadata' => array_filter([
                'full_name' => $full_name,
                'company'   => $company ?? '',
            ]),
        ]);

        if (in_array($res['code'], [200, 201])) return null;

        $msg = $res['data']['msg']
            ?? $res['data']['message']
            ?? $res['data']['error_description']
            ?? '';

        if ($res['code'] === 422 || stripos($msg, 'already') !== false) {
            return 'duplicate';
        }

        error_log('Supabase register error ' . $res['code'] . ': ' . json_encode($res['data']));
        return $msg ?: 'HTTP ' . $res['code'];
    }

    /**
     * Sign in via Supabase password grant.
     * Returns ['email', 'full_name', 'company'] on success, null on bad credentials.
     */
    public function login(string $email, string $password): ?array
    {
        $res = $this->post('/auth/v1/token?grant_type=password', [
            'email'    => strtolower($email),
            'password' => $password,
        ]);

        if ($res['code'] !== 200 || empty($res['data']['user'])) {
            error_log('Supabase login error ' . $res['code'] . ': ' . json_encode($res['data']));
            return null;
        }

        $user = $res['data']['user'];
        $meta = $user['user_metadata'] ?? [];

        return [
            'email'     => $user['email'],
            'full_name' => $meta['full_name'] ?? explode('@', $user['email'])[0],
            'company'   => ($meta['company'] ?? '') ?: null,
        ];
    }
}
