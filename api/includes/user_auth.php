<?php
/**
 * Self-contained developer auth using PostgreSQL + bcrypt.
 * No third-party auth service required.
 */
require_once __DIR__ . '/../config.php';

function _get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT            => 10,
    ]);

    // Create table on first use — safe to call every time (IF NOT EXISTS)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS console_users (
            id           SERIAL PRIMARY KEY,
            email        VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(256) NOT NULL,
            full_name    VARCHAR(150) NOT NULL,
            company      VARCHAR(150),
            created_at   TIMESTAMP DEFAULT NOW()
        )
    ");

    return $pdo;
}

/**
 * Register a new developer.
 * Returns null on success, 'duplicate' if email taken, throws on DB error.
 */
function auth_register(string $email, string $password, string $full_name, ?string $company): ?string
{
    $pdo  = _get_pdo();
    $hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO console_users (email, password_hash, full_name, company)
             VALUES (:email, :hash, :name, :company)'
        );
        $stmt->execute([
            ':email'   => strtolower($email),
            ':hash'    => $hash,
            ':name'    => $full_name,
            ':company' => $company,
        ]);
        return null;
    } catch (PDOException $e) {
        // 23505 = unique_violation in PostgreSQL
        if ($e->getCode() === '23505') return 'duplicate';
        throw new RuntimeException('Database error: ' . $e->getMessage());
    }
}

/**
 * Authenticate a developer.
 * Returns ['email', 'full_name', 'company'] on success, null on bad credentials.
 */
function auth_login(string $email, string $password): ?array
{
    $pdo  = _get_pdo();
    $stmt = $pdo->prepare(
        'SELECT email, password_hash, full_name, company
         FROM console_users WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => strtolower($email)]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($password, $row['password_hash'])) {
        return null;
    }

    return [
        'email'     => $row['email'],
        'full_name' => $row['full_name'],
        'company'   => $row['company'],
    ];
}
