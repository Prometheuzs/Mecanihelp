<?php
declare(strict_types=1);

class ChamadoService {
    private ChamadoRepository $chamadoRepo;
    private VeiculoRepository $veiculoRepo;

    public function __construct() {
        $this->chamadoRepo = new ChamadoRepository();
        $this->veiculoRepo = new VeiculoRepository();
    }

    public function criar(array $dados, array $usuarioLogado, int $idCliente): int {
        // RN02: Todo chamado deve estar vinculado a um veículo cadastrado. O veículo deve pertencer ao solicitante.
        $veiculo = $this->veiculoRepo->findById((int)$dados['id_veiculo']);
        if (!$veiculo || $veiculo['id_cliente'] !== $idCliente) {
            throw new Exception("Veículo inválido ou não pertence ao usuário.");
        }

        $id = $this->chamadoRepo->inserir([
            'descricao_problema' => $dados['descricao_problema'],
            'prioridade' => $dados['prioridade'],
            'id_cliente' => $idCliente,
            'id_veiculo' => $veiculo['id_veiculo']
        ]);

        $this->chamadoRepo->registrarHistoricoChamado($id, $usuarioLogado['id'], 'Abertura', 'Chamado aberto pelo solicitante');
        
        return $id;
    }

    public function assumir(int $idChamado, array $usuarioLogado): void {
        $chamado = $this->chamadoRepo->findById($idChamado);
        if (!$chamado || $chamado['status'] !== 'aberto') {
            throw new Exception("Chamado inválido ou já está em atendimento.");
        }

        $sucesso = $this->chamadoRepo->assumir($idChamado, $usuarioLogado['id']);
        if (!$sucesso) {
            throw new Exception("Falha ao assumir. Outro atendente já pode ter assumido (RN04).");
        }

        $this->chamadoRepo->registrarHistoricoStatus($idChamado, $usuarioLogado['id'], 'aberto', 'em_analise', 'Atendente assumiu o chamado.');
        $this->chamadoRepo->registrarHistoricoChamado($idChamado, $usuarioLogado['id'], 'Assumir', 'Atendente assumiu o chamado');
    }

    public function adicionarAndamento(int $idChamado, string $descricao, array $usuarioLogado): void {
        $chamado = $this->chamadoRepo->findById($idChamado);
        if (!$chamado || $chamado['status'] === 'encerrado') {
            throw new Exception("Chamado inválido ou encerrado.");
        }

        // RN07: Todo andamento registra automaticamente usuário e data/hora (passado pelo banco).
        $this->chamadoRepo->registrarAndamento($idChamado, $usuarioLogado['id'], $descricao);
        $this->chamadoRepo->registrarHistoricoChamado($idChamado, $usuarioLogado['id'], 'Andamento', 'Novo andamento registrado');
    }

    public function alterarStatus(int $idChamado, string $novoStatus, string $descricao, array $usuarioLogado): void {
        $chamado = $this->chamadoRepo->findById($idChamado);
        if (!$chamado || $chamado['status'] === 'encerrado') {
            throw new Exception("Chamado já está encerrado ou é inválido.");
        }

        // RN03: Somente o atendente responsável (ou o administrador) pode alterar o status
        if ($usuarioLogado['perfil'] !== 'administrador' && $chamado['id_atendente'] !== $usuarioLogado['id']) {
            throw new Exception("Apenas o atendente responsável ou admin pode alterar o status.");
        }

        // RN06: Toda mudança de status exige descrição.
        if (trim($descricao) === '') {
            throw new Exception("A descrição para a mudança de status é obrigatória.");
        }

        if ($novoStatus === 'encerrado') {
            // Delegação para o método específico que trata encerramento com regras
            $this->encerrar($idChamado, $descricao, $usuarioLogado);
            return;
        }

        $this->chamadoRepo->atualizarStatus($idChamado, $novoStatus);
        $this->chamadoRepo->registrarHistoricoStatus($idChamado, $usuarioLogado['id'], $chamado['status'], $novoStatus, $descricao);
        $this->chamadoRepo->registrarHistoricoChamado($idChamado, $usuarioLogado['id'], 'Alteração de Status', "Status alterado de {$chamado['status']} para {$novoStatus}");
    }

    public function encerrar(int $idChamado, string $motivo, array $usuarioLogado): void {
        $chamado = $this->chamadoRepo->findById($idChamado);
        
        // RN08: Chamados nunca são excluídos; encerramento exige motivo.
        if (trim($motivo) === '') {
            throw new Exception("Motivo de encerramento é obrigatório.");
        }

        // RN01: Só encerra após confirmação da retirada (simulação: se a flag vier do banco, mas aqui podemos forçar o status ou requerer no form)
        // Para simplificar no MVP e permitir a demonstração fluida, checaremos o status (ex: status tem que ser concluido antes de encerrar)
        // Ou implementamos a checagem do campo 'retirada_confirmada'.
        if (!(bool)$chamado['retirada_confirmada']) {
            throw new Exception("RN01: Não é possível encerrar sem a confirmação de retirada do veículo pelo cliente.");
        }

        $this->chamadoRepo->atualizarStatus($idChamado, 'encerrado', $motivo);
        $this->chamadoRepo->registrarHistoricoStatus($idChamado, $usuarioLogado['id'], $chamado['status'], 'encerrado', $motivo);
        $this->chamadoRepo->registrarHistoricoChamado($idChamado, $usuarioLogado['id'], 'Encerramento', 'Chamado encerrado com sucesso');
    }
}
