<?php
declare(strict_types=1);

class Csrf {
    public static function generateToken(): string {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyToken(?string $token): bool {
        if (!$token || $token !== Session::get('csrf_token')) {
            return false;
        }
        return true;
    }

    public static function requireToken(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!self::verifyToken($_POST['csrf_token'] ?? '')) {
                die("Token CSRF inválido.");
            }
        }
    }
}
