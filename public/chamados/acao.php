<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requirePerfil(['atendente', 'administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/chamados/index.php');
}

$acao = $_POST['acao'] ?? '';
$idChamado = (int)($_POST['id_chamado'] ?? 0);
$usuario = Auth::usuario();
$service = new ChamadoService();

if ($idChamado === 0) {
    Flash::set('danger', 'ID do chamado inválido.');
    redirect('/chamados/index.php');
}

try {
    if ($acao === 'assumir') {
        $service->assumir($idChamado, $usuario);
        Flash::set('success', 'Você assumiu o chamado!');
        
    } elseif ($acao === 'andamento') {
        $descricao = $_POST['descricao'] ?? '';
        if (trim($descricao) === '') throw new Exception("Descrição do andamento é obrigatória.");
        $service->adicionarAndamento($idChamado, $descricao, $usuario);
        Flash::set('success', 'Andamento registrado com sucesso!');
        
    } elseif ($acao === 'status') {
        $novoStatus = $_POST['status'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $retirada = isset($_POST['retirada_confirmada']) && $_POST['retirada_confirmada'] === '1';

        // Atualizar flag de retirada no banco (simplificação direta aqui para não poluir o service)
        $repo = new ChamadoRepository();
        $repo->getDb()->prepare("UPDATE chamado SET retirada_confirmada = :r WHERE id_chamado = :id")
             ->execute(['r' => $retirada ? 1 : 0, 'id' => $idChamado]);

        if ($novoStatus === 'encerrado') {
            $service->encerrar($idChamado, $descricao, $usuario);
            Flash::set('success', 'Chamado ENCERRADO com sucesso!');
        } else {
            $service->alterarStatus($idChamado, $novoStatus, $descricao, $usuario);
            Flash::set('success', 'Status atualizado com sucesso!');
        }
    }
} catch (Exception $e) {
    Flash::set('danger', $e->getMessage());
}

redirect('/chamados/ver.php?id=' . $idChamado);
