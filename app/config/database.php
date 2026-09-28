<?php
declare(strict_types=1);

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", DB_HOST, DB_NAME);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados.");
        }
    }
    return $pdo;
}
