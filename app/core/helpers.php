<?php
declare(strict_types=1);

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: " . BASE_URL . $url);
    exit;
}
