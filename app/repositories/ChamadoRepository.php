<?php
declare(strict_types=1);

class ChamadoRepository {
    private PDO $db;
    public function __construct() { $this->db = getDbConnection(); }

    public function getDb(): PDO { return $this->db; }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, v.placa, v.modelo, u.nome as atendente_nome 
            FROM chamado c 
            JOIN veiculo v ON c.id_veiculo = v.id_veiculo 
            LEFT JOIN usuario u ON c.id_atendente = u.id_usuario 
            WHERE c.id_chamado = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findAllByCliente(int $idCliente): array {
        $stmt = $this->db->prepare("
            SELECT c.*, v.placa 
            FROM chamado c 
            JOIN veiculo v ON c.id_veiculo = v.id_veiculo 
            WHERE c.id_cliente = :id_cliente 
            ORDER BY c.data_abertura DESC
        ");
        $stmt->execute(['id_cliente' => $idCliente]);
        return $stmt->fetchAll();
    }

    public function findAllDisponiveisEAtendente(int $idAtendente): array {
        $stmt = $this->db->prepare("
            SELECT c.*, v.placa, u.nome as atendente_nome
            FROM chamado c 
            JOIN veiculo v ON c.id_veiculo = v.id_veiculo 
            LEFT JOIN usuario u ON c.id_atendente = u.id_usuario
            WHERE c.id_atendente IS NULL OR c.id_atendente = :id_atendente
            ORDER BY FIELD(c.prioridade, 'alta', 'media', 'baixa'), c.data_abertura DESC
        ");
        $stmt->execute(['id_atendente' => $idAtendente]);
        return $stmt->fetchAll();
    }

    public function findAll(): array {
        $stmt = $this->db->query("
            SELECT c.*, v.placa, u.nome as atendente_nome
            FROM chamado c 
            JOIN veiculo v ON c.id_veiculo = v.id_veiculo 
            LEFT JOIN usuario u ON c.id_atendente = u.id_usuario
            ORDER BY c.data_abertura DESC
        ");
        return $stmt->fetchAll();
    }

    public function inserir(array $dados): int {
        $stmt = $this->db->prepare("
            INSERT INTO chamado (descricao_problema, prioridade, id_cliente, id_veiculo) 
            VALUES (:descricao, :prioridade, :id_cliente, :id_veiculo)
        ");
        $stmt->execute([
            'descricao' => $dados['descricao_problema'],
            'prioridade' => $dados['prioridade'],
            'id_cliente' => $dados['id_cliente'],
            'id_veiculo' => $dados['id_veiculo']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function assumir(int $idChamado, int $idAtendente): bool {
        // RN04: UPDATE com restrição para garantir exclusividade atômica
        $stmt = $this->db->prepare("UPDATE chamado SET id_atendente = :atendente, status = 'em_analise' WHERE id_chamado = :id AND id_atendente IS NULL");
        $stmt->execute(['atendente' => $idAtendente, 'id' => $idChamado]);
        return $stmt->rowCount() > 0;
    }

    public function atualizarStatus(int $idChamado, string $status, ?string $motivo = null): void {
        if ($status === 'encerrado') {
            $stmt = $this->db->prepare("UPDATE chamado SET status = :status, motivo_encerramento = :motivo, data_encerramento = CURRENT_TIMESTAMP WHERE id_chamado = :id");
            $stmt->execute(['status' => $status, 'motivo' => $motivo, 'id' => $idChamado]);
        } else {
            $stmt = $this->db->prepare("UPDATE chamado SET status = :status WHERE id_chamado = :id");
            $stmt->execute(['status' => $status, 'id' => $idChamado]);
        }
    }

    public function registrarHistoricoChamado(int $idChamado, int $idUsuario, string $acao, string $descricao): void {
        $stmt = $this->db->prepare("INSERT INTO historico_chamado (id_chamado, id_usuario, acao, descricao) VALUES (:id, :usr, :acao, :desc)");
        $stmt->execute(['id' => $idChamado, 'usr' => $idUsuario, 'acao' => $acao, 'desc' => $descricao]);
    }

    public function registrarHistoricoStatus(int $idChamado, int $idUsuario, ?string $statusAntigo, string $statusNovo, string $descricao): void {
        $stmt = $this->db->prepare("INSERT INTO historico_status (id_chamado, id_usuario, status_anterior, status_novo, descricao) VALUES (:id, :usr, :antigo, :novo, :desc)");
        $stmt->execute(['id' => $idChamado, 'usr' => $idUsuario, 'antigo' => $statusAntigo, 'novo' => $statusNovo, 'desc' => $descricao]);
    }

    public function registrarAndamento(int $idChamado, int $idUsuario, string $descricao): void {
        $stmt = $this->db->prepare("INSERT INTO andamento (id_chamado, id_usuario, descricao) VALUES (:id, :usr, :desc)");
        $stmt->execute(['id' => $idChamado, 'usr' => $idUsuario, 'desc' => $descricao]);
    }

    public function listarAndamentos(int $idChamado): array {
        $stmt = $this->db->prepare("SELECT a.*, u.nome as usuario_nome FROM andamento a JOIN usuario u ON a.id_usuario = u.id_usuario WHERE id_chamado = :id ORDER BY data_hora DESC");
        $stmt->execute(['id' => $idChamado]);
        return $stmt->fetchAll();
    }

    public function listarHistorico(int $idChamado): array {
        $stmt = $this->db->prepare("SELECT h.*, u.nome as usuario_nome FROM historico_chamado h JOIN usuario u ON h.id_usuario = u.id_usuario WHERE id_chamado = :id ORDER BY data_hora DESC");
        $stmt->execute(['id' => $idChamado]);
        return $stmt->fetchAll();
    }
}
