<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

Auth::requireLogin();
$usuario = Auth::usuario();

$pageTitle = "Dashboard";
require_once __DIR__ . '/../app/views/layouts/header.php';
require_once __DIR__ . '/../app/views/layouts/menu.php';
?>

<main class="main-content">
    <div class="container">
        <?php require __DIR__ . '/../app/views/layouts/flash.php'; ?>

        <h2 style="margin-bottom: var(--space-2);">Dashboard</h2>
        <p style="color: var(--text-muted); margin-bottom: var(--space-4);">Bem-vindo ao sistema de chamados da oficina.</p>

        <div class="card">
            <h3 style="margin-bottom: var(--space-3);">Resumo do seu acesso</h3>
            <p>Seu perfil é <strong><?= e($usuario['perfil']) ?></strong>. Utilize o menu lateral para navegar nas opções disponíveis para você.</p>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../app/views/layouts/footer.php'; ?>
