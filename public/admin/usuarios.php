<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

// RN10: Somente o administrador cria usuários (e acessa esta página)
Auth::requirePerfil(['administrador']);

$repo = new UsuarioRepository();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? '';

    if (Validator::required($nome) && Validator::required($login) && Validator::required($senha) && Validator::required($perfil)) {
        $sucesso = $repo->inserir([
            'nome' => $nome,
            'login' => $login,
            'senha' => $senha,
            'perfil' => $perfil
        ]);

        if ($sucesso) {
            Flash::set('success', 'Usuário cadastrado com sucesso!');
        } else {
            Flash::set('danger', 'Erro ao cadastrar. O login informado já pode estar em uso.');
        }
    } else {
        Flash::set('danger', 'Preencha todos os campos obrigatórios.');
    }
    
    redirect('/admin/usuarios.php');
}

$usuarios = $repo->findAll();

$pageTitle = "Gestão de Usuários";
require_once __DIR__ . '/../../app/views/layouts/header.php';
require_once __DIR__ . '/../../app/views/layouts/menu.php';
?>

<main class="main-content">
    <div class="container">
        <?php require __DIR__ . '/../../app/views/layouts/flash.php'; ?>
        
        <h2>Gestão de Usuários (Administrador)</h2>
        <p class="text-muted" style="margin-bottom: var(--space-4);">Crie novos atendentes ou administradores.</p>

        <div class="card">
            <h3>Cadastrar Novo Usuário</h3>
            <form method="POST" action="usuarios.php" style="margin-top: var(--space-3);">
                <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
                
                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label" for="nome">Nome Completo</label>
                        <input class="form-control" type="text" name="nome" id="nome" required>
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label" for="login">Login de Acesso</label>
                        <input class="form-control" type="text" name="login" id="login" required>
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label" for="senha">Senha Inicial</label>
                        <input class="form-control" type="password" name="senha" id="senha" required>
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label" for="perfil">Perfil</label>
                        <select class="form-control" name="perfil" id="perfil" required>
                            <option value="atendente">Atendente</option>
                            <option value="administrador">Administrador</option>
                            <!-- Solicitante normalmente se cadastraria sozinho, 
                                 mas deixamos disponível se o admin quiser criar manualmente -->
                            <option value="solicitante">Solicitante</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="margin-top: var(--space-2);">Salvar Usuário</button>
            </form>
        </div>

        <div class="card" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Login</th>
                        <th>Perfil</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>#<?= e((string)$u['id_usuario']) ?></td>
                            <td><?= e($u['nome']) ?></td>
                            <td><?= e($u['login']) ?></td>
                            <td>
                                <span class="badge" style="background: var(--gray-200); color: var(--text);">
                                    <?= e(ucfirst($u['perfil'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $u['ativo'] ? 'concluido' : 'encerrado' ?>">
                                    <?= $u['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../app/views/layouts/footer.php'; ?>
