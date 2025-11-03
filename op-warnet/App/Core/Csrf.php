<?php
class Csrf {
    private static string $tokenName = '_csrf_token';

    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }

    public static function getToken(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[self::$tokenName] ?? null;
    }

    public static function verify(string $submittedToken): bool {
        $token = self::getToken();
        return $token !== null && hash_equals($token, $submittedToken);
    }

    public static function verifyOrFail(?string $submittedToken): void {
        if ($submittedToken === null || !self::verify($submittedToken)) {
            http_response_code(403);
            self::clearToken();
            die('CSRF token validation failed. Silakan coba lagi.');
        }
    }

    public static function input(): string {
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . self::generateToken() . '">';
    }

    public static function clearToken(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[self::$tokenName]);
    }
}