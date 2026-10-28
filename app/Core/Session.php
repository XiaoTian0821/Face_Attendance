<?php
/**
 * Session management
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function delete(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    /**
     * Set a flash message (available only on next request)
     * @param string $type - 'success' or 'error'
     * @param string $message
     */
    public static function flash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear all pending flash messages
     * @return array of ['type' => string, 'message' => string]
     */
    public static function getFlashes(): array
    {
        self::start();
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    public static function user(): ?array
    {
        return self::get('user');
    }

    public static function isLoggedIn(): bool
    {
        return self::get('user_id') !== null;
    }

    public static function role(): ?string
    {
        return self::get('role');
    }
}
