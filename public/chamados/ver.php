<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireLogin();
$usuario = Auth::usuario();

$idChamado = (int)($_GET['id'] ?? 0);
$chamadoRepo = new ChamadoRepository();
$chamado = $chamadoRepo->findById($idChamado);

if (!$chamado) {
    Flash::set('danger', 'Chamado não encontrado.');
    redirect('/chamados/index.php');
}

// RN09: Solicitante só enxerga próprios chamados
if ($usuario['perfil'] === 'solicitante') {
    $clienteRepo = new ClienteRepository();
    $cliente = $clienteRepo->findByUsuarioId($usuario['id']);
    if (!$cliente || $chamado['id_cliente'] !== $cliente['id_cliente']) {
        http_response_code(403);
        die("Acesso Negado.");
    }
}

$andamentos = $chamadoRepo->listarAndamentos($idChamado);
$historico = $chamadoRepo->listarHistorico($idChamado);

$pageTitle = "Chamado #" . $idChamado;
require_once __DIR__ . '/../../app/views/layouts/header.php';
require_once __DIR__ . '/../../app/views/layouts/menu.php';
?>

<main class="main-content">
    <div class="container">
        <?php require __DIR__ . '/../../app/views/layouts/flash.php'; ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
            <h2>Chamado #<?= e((string)$chamado['id_chamado']) ?></h2>
            <span class="badge badge-<?= e($chamado['status']) ?>" style="font-size: 1.25rem;">
                <?= e(ucfirst(str_replace('_', ' ', $chamado['status']))) ?>
            </span>
        </div>

        <div class="card">
            <h3>Detalhes</h3>
            <p><strong>Veículo:</strong> <?= e($chamado['modelo']) ?> (<?= e($chamado['placa']) ?>)</p>
            <p><strong>Prioridade:</strong> <?= e(ucfirst($chamado['prioridade'])) ?></p>
            <p><strong>Abertura:</strong> <?= e(date('d/m/Y H:i', strtotime($chamado['data_abertura']))) ?></p>
            <p><strong>Atendente Responsável:</strong> <?= e($chamado['atendente_nome'] ?? 'Aguardando') ?></p>
            
            <div style="margin-top: var(--space-3); padding: var(--space-3); background: var(--bg); border-radius: var(--radius);">
                <strong>Problema relatado:</strong><br>
                <?= nl2br(e($chamado['descricao_problema'])) ?>
            </div>
            
            <?php if ($chamado['status'] === 'encerrado'): ?>
                <div style="margin-top: var(--space-3); padding: var(--space-3); background: hsl(150, 55%, 90%); color: var(--success); border-radius: var(--radius);">
                    <strong>Motivo do Encerramento (<?= e(date('d/m/Y H:i', strtotime($chamado['data_encerramento']))) ?>):</strong><br>
                    <?= nl2br(e($chamado['motivo_encerramento'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ações do Atendente/Admin -->
        <?php if (in_array($usuario['perfil'], ['atendente', 'administrador'])): ?>
            <div class="card">
                <h3>Ações</h3>
                
                <?php if ($chamado['status'] === 'aberto' && $chamado['id_atendente'] === null): ?>
                    <form method="POST" action="acao.php">
                        <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
                        <input type="hidden" name="acao" value="assumir">
                        <input type="hidden" name="id_chamado" value="<?= e((string)$chamado['id_chamado']) ?>">
                        <button type="submit" class="btn btn-primary">Assumir Chamado</button>
                    </form>
                <?php elseif ($chamado['id_atendente'] === $usuario['id'] || $usuario['perfil'] === 'administrador'): ?>
                    
                    <?php if ($chamado['status'] !== 'encerrado'): ?>
                        <!-- Adicionar Andamento -->
                        <form method="POST" action="acao.php" style="margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border);">
                            <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
                            <input type="hidden" name="acao" value="andamento">
                            <input type="hidden" name="id_chamado" value="<?= e((string)$chamado['id_chamado']) ?>">
                            <div class="form-group">
                                <label class="form-label" for="descricao_andamento">Registrar Andamento (Público)</label>
                                <textarea class="form-control" name="descricao" id="descricao_andamento" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary">Adicionar Andamento</button>
                        </form>

                        <!-- Alterar Status / Encerrar -->
                        <form method="POST" action="acao.php">
                            <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
                            <input type="hidden" name="acao" value="status">
                            <input type="hidden" name="id_chamado" value="<?= e((string)$chamado['id_chamado']) ?>">
                            
                            <div class="form-group">
                                <label class="form-label" for="novo_status">Mudar Status</label>
                                <select class="form-control" name="status" id="novo_status" required>
                                    <option value="em_analise" <?= $chamado['status'] === 'em_analise' ? 'selected' : '' ?>>Em Análise</option>
                                    <option value="em_execucao" <?= $chamado['status'] === 'em_execucao' ? 'selected' : '' ?>>Em Execução</option>
                                    <option value="aguardando_peca" <?= $chamado['status'] === 'aguardando_peca' ? 'selected' : '' ?>>Aguardando Peça</option>
                                    <option value="concluido" <?= $chamado['status'] === 'concluido' ? 'selected' : '' ?>>Concluído (Pronto para retirar)</option>
                                    <option value="encerrado" style="font-weight:bold; color:var(--danger);">Encerrar Chamado</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="desc_status">Justificativa / Motivo</label>
                                <textarea class="form-control" name="descricao" id="desc_status" required placeholder="Obrigatório em qualquer mudança de status"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="retirada_confirmada" value="1" <?= $chamado['retirada_confirmada'] ? 'checked' : '' ?>> 
                                    Confirma retirada do veículo (Obrigatório para encerrar)
                                </label>
                            </div>

                            <button type="submit" class="btn btn-warning" style="background: var(--warning); color: #fff;">Salvar Status</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Histórico e Andamentos -->
        <div style="display: flex; gap: var(--space-4);">
            <div class="card" style="flex: 1;">
                <h3 style="margin-bottom: var(--space-4);">Diário de Andamentos</h3>
                <?php if (empty($andamentos)): ?>
                    <p class="text-muted">Nenhum andamento registrado.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($andamentos as $a): ?>
                            <div class="timeline-item">
                                <div class="timeline-date"><?= e(date('d/m/Y H:i', strtotime($a['data_hora']))) ?> por <?= e($a['usuario_nome']) ?></div>
                                <p><?= nl2br(e($a['descricao'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card" style="flex: 1;">
                <h3 style="margin-bottom: var(--space-4);">Histórico Completo (Auditoria)</h3>
                <div class="timeline">
                    <?php foreach ($historico as $h): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= e(date('d/m/Y H:i', strtotime($h['data_hora']))) ?> por <?= e($h['usuario_nome']) ?></div>
                            <strong><?= e($h['acao']) ?></strong>
                            <p style="font-size: 0.875rem; color: var(--text-muted);"><?= nl2br(e($h['descricao'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../app/views/layouts/footer.php'; ?>
