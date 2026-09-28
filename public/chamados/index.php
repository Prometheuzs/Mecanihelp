<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

Auth::requireLogin();
$usuario = Auth::usuario();
$chamadoRepo = new ChamadoRepository();
$clienteRepo = new ClienteRepository();

// RN09: Solicitante só enxerga os próprios chamados
if ($usuario['perfil'] === 'solicitante') {
    $cliente = $clienteRepo->findByUsuarioId($usuario['id']);
    if (!$cliente) die("Erro: Cadastro de cliente não encontrado para este usuário.");
    $chamados = $chamadoRepo->findAllByCliente($cliente['id_cliente']);
} elseif ($usuario['perfil'] === 'atendente') {
    $chamados = $chamadoRepo->findAllDisponiveisEAtendente($usuario['id']);
} else {
    // Admin
    $chamados = $chamadoRepo->findAll();
}

$pageTitle = "Chamados";
require_once __DIR__ . '/../../app/views/layouts/header.php';
require_once __DIR__ . '/../../app/views/layouts/menu.php';
?>

<main class="main-content">
    <div class="container">
        <?php require __DIR__ . '/../../app/views/layouts/flash.php'; ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
            <h2>Chamados</h2>
            <?php if ($usuario['perfil'] === 'solicitante'): ?>
                <a href="novo.php" class="btn btn-primary">+ Novo Chamado</a>
            <?php endif; ?>
        </div>

        <?php if (empty($chamados)): ?>
            <div class="card">
                <p>Nenhum chamado encontrado.</p>
            </div>
        <?php else: ?>
            <div class="card" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Veículo</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Atendente</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chamados as $c): ?>
                            <tr>
                                <td>#<?= e((string)$c['id_chamado']) ?></td>
                                <td><?= e($c['placa']) ?></td>
                                <td>
                                    <span class="badge badge-<?= e($c['status']) ?>">
                                        <?= e(ucfirst(str_replace('_', ' ', $c['status']))) ?>
                                    </span>
                                </td>
                                <td><?= e(ucfirst($c['prioridade'])) ?></td>
                                <td><?= e($c['atendente_nome'] ?? 'Aguardando') ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime($c['data_abertura']))) ?></td>
                                <td>
                                    <a href="ver.php?id=<?= e((string)$c['id_chamado']) ?>" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../../app/views/layouts/footer.php'; ?>
