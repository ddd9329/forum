<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $cookieParams = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params($cookieParams);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', $cookieParams['secure'], true);
    }
    session_start();
}

require_once __DIR__ . '/db.php';

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_validate($token): bool {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'][] = ['t' => $type, 'm' => $msg];
}
function flashes(): array {
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}
function is_logged_in(): bool {
    return current_user() !== null;
}
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] ?? 'user') === 'admin';
}
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}
function base_url(string $path = ''): string {
    $base = defined('BASE_URL') && BASE_URL !== '' ? BASE_URL : (isset($_SERVER['HTTP_HOST']) ? ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ) : '');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function login(string $usernameOrEmail, string $password): bool {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u OR email = :e LIMIT 1");
    $stmt->execute([':u' => $usernameOrEmail, ':e' => $usernameOrEmail]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        return true;
    }
    return false;
}

function register_user(string $username, string $email, string $password): bool {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)");
        $stmt->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}
