<?php 
declare(strict_types=1); 
$usuario = Auth::usuario();
?>
<aside style="width: 250px; background-color: var(--surface); border-right: 1px solid var(--border); padding: var(--space-4); display: flex; flex-direction: column;">
    <h2 style="color: var(--primary); margin-bottom: var(--space-4);">Mecanihelp</h2>
    
    <div style="margin-bottom: var(--space-4); border-bottom: 1px solid var(--border); padding-bottom: var(--space-3);">
        <strong style="color: var(--text);"><?= e($usuario['nome'] ?? '') ?></strong><br>
        <small style="color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;"><?= e($usuario['perfil'] ?? '') ?></small>
    </div>

    <nav style="display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
        <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-secondary" style="text-align: left; background-color: transparent; border: 1px solid transparent;">Dashboard</a>
        
        <?php if (($usuario['perfil'] ?? '') === 'solicitante'): ?>
            <a href="<?= BASE_URL ?>/chamados/index.php" class="btn btn-secondary" style="text-align: left; background-color: transparent;">Meus Chamados</a>
            <a href="<?= BASE_URL ?>/chamados/novo.php" class="btn btn-primary" style="text-align: center; margin-top: var(--space-2);">+ Novo Chamado</a>
        <?php elseif (($usuario['perfil'] ?? '') === 'atendente'): ?>
            <a href="<?= BASE_URL ?>/chamados/index.php" class="btn btn-secondary" style="text-align: left; background-color: transparent;">Painel de Atendimento</a>
        <?php elseif (($usuario['perfil'] ?? '') === 'administrador'): ?>
            <a href="<?= BASE_URL ?>/chamados/index.php" class="btn btn-secondary" style="text-align: left; background-color: transparent;">Todos os Chamados</a>
            <a href="<?= BASE_URL ?>/admin/usuarios.php" class="btn btn-secondary" style="text-align: left; background-color: transparent;">Gerenciar Usuários</a>
        <?php endif; ?>
    </nav>
    
    <div style="margin-top: auto;">
        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-danger" style="display: block; width: 100%;">Sair</a>
    </div>
</aside>
