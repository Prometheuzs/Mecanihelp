<?php
declare(strict_types=1);

class VeiculoRepository {
    private PDO $db;
    public function __construct() { $this->db = getDbConnection(); }

    public function findByClienteId(int $idCliente): array {
        $stmt = $this->db->prepare("SELECT * FROM veiculo WHERE id_cliente = :id");
        $stmt->execute(['id' => $idCliente]);
        return $stmt->fetchAll();
    }

    public function findById(int $idVeiculo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM veiculo WHERE id_veiculo = :id");
        $stmt->execute(['id' => $idVeiculo]);
        return $stmt->fetch() ?: null;
    }
}
