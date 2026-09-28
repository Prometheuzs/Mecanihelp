<?php
declare(strict_types=1);

class Flash {
    public static function set(string $type, string $message): void {
        Session::set('flash', ['type' => $type, 'message' => $message]);
    }

    public static function get(): ?array {
        $flash = Session::get('flash');
        if ($flash) {
            Session::remove('flash');
        }
        return $flash;
    }
}
