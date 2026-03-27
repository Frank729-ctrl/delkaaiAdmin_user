<?php
/**
 * Minimal Supabase REST client for developer account management.
 * Uses the service-role key so it bypasses RLS and works on all tables.
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

    private function request(string $method, string $table, array $body = [], array $query = []): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        if ($query) $url .= '?' . http_build_query($query);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'apikey: '               . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json',
                'Accept: application/json',
                'Prefer: return=representation',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) throw new RuntimeException('Connection error: ' . $err);

        $data = json_decode($raw, true);
        return ['code' => $code, 'data' => $data];
    }

    /** Find a developer account by email. Returns row array or null. */
    public function findUser(string $email): ?array
    {
        $res = $this->request('GET', 'developer_accounts', [], [
            'email'  => 'eq.' . strtolower($email),
            'select' => '*',
            'limit'  => '1',
        ]);
        return ($res['code'] === 200 && !empty($res['data'])) ? $res['data'][0] : null;
    }

    /** Insert a new developer account. Returns true on success, false on duplicate. */
    public function createUser(string $email, string $pw_hash, string $full_name, ?string $company): bool
    {
        $body = [
            'email'         => strtolower($email),
            'password_hash' => $pw_hash,
            'full_name'     => $full_name,
            'is_active'     => true,
            'is_verified'   => false,
            'created_at'    => gmdate('Y-m-d\TH:i:s'),
        ];
        if ($company) $body['company'] = $company;

        $res = $this->request('POST', 'developer_accounts', $body);
        return $res['code'] === 201;
    }

    /** Update last_login_at (best-effort, never throws). */
    public function touchLastLogin(string $email): void
    {
        try {
            $this->request('PATCH', 'developer_accounts',
                ['last_login_at' => gmdate('Y-m-d\TH:i:s')],
                ['email' => 'eq.' . strtolower($email)]
            );
        } catch (RuntimeException) {}
    }
}
