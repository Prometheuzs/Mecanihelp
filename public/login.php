<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if (Auth::check()) {
    redirect('/dashboard.php');
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (Validator::required($login) && Validator::required($senha)) {
        $repo = new UsuarioRepository();
        $usuario = $repo->autenticar($login, $senha);

        if ($usuario) {
            Auth::login($usuario);
            redirect('/dashboard.php');
        } else {
            $erro = "Login ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}

$pageTitle = "Login";
require_once __DIR__ . '/../app/views/layouts/header.php';
?>

<div style="display: flex; justify-content: center; align-items: center; width: 100vw; height: 100vh; background-color: var(--bg);">
    <div class="card" style="width: 100%; max-width: 400px;">
        <h2 style="color: var(--primary); margin-bottom: var(--space-1); text-align: center;">Mecanihelp</h2>
        <p style="text-align: center; color: var(--text-muted); margin-bottom: var(--space-4);">Acesso ao sistema</p>
        
        <?php require __DIR__ . '/../app/views/layouts/flash.php'; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= e($erro) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
            
            <div class="form-group">
                <label class="form-label" for="login">Login</label>
                <input class="form-control" type="text" id="login" name="login" required autofocus>
            </div>
            
            <div class="form-group" style="margin-bottom: var(--space-4);">
                <label class="form-label" for="senha">Senha</label>
                <input class="form-control" type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../app/views/layouts/footer.php'; ?>
