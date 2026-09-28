<?php
declare(strict_types=1);

class ClienteRepository {
    private PDO $db;
    public function __construct() { $this->db = getDbConnection(); }

    public function findByUsuarioId(int $idUsuario): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE id_usuario = :id");
        $stmt->execute(['id' => $idUsuario]);
        return $stmt->fetch() ?: null;
    }
}
