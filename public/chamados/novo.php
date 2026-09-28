<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requirePerfil(['solicitante']);
$usuario = Auth::usuario();

$veiculoRepo = new VeiculoRepository();
$clienteRepo = new ClienteRepository();
$cliente = $clienteRepo->findByUsuarioId($usuario['id']);

if (!$cliente) {
    Flash::set('danger', 'Perfil de cliente não encontrado.');
    redirect('/dashboard.php');
}

$veiculos = $veiculoRepo->findByClienteId($cliente['id_cliente']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_veiculo = $_POST['id_veiculo'] ?? '';
    $prioridade = $_POST['prioridade'] ?? 'baixa';
    $descricao = $_POST['descricao_problema'] ?? '';

    if (Validator::required($id_veiculo) && Validator::required($descricao)) {
        try {
            $service = new ChamadoService();
            $service->criar([
                'id_veiculo' => $id_veiculo,
                'prioridade' => $prioridade,
                'descricao_problema' => $descricao
            ], $usuario, $cliente['id_cliente']);
            
            Flash::set('success', 'Chamado aberto com sucesso!');
            redirect('/chamados/index.php');
        } catch (Exception $e) {
            Flash::set('danger', $e->getMessage());
        }
    } else {
        Flash::set('danger', 'Preencha todos os campos obrigatórios.');
    }
}

$pageTitle = "Novo Chamado";
require_once __DIR__ . '/../../app/views/layouts/header.php';
require_once __DIR__ . '/../../app/views/layouts/menu.php';
?>

<main class="main-content">
    <div class="container">
        <?php require __DIR__ . '/../../app/views/layouts/flash.php'; ?>
        
        <h2 style="margin-bottom: var(--space-4);">Abrir Novo Chamado</h2>

        <div class="card">
            <form method="POST" action="novo.php">
                <input type="hidden" name="csrf_token" value="<?= e(Csrf::generateToken()) ?>">
                
                <div class="form-group">
                    <label class="form-label" for="id_veiculo">Veículo</label>
                    <select class="form-control" name="id_veiculo" id="id_veiculo" required>
                        <option value="">-- Selecione o veículo --</option>
                        <?php foreach ($veiculos as $v): ?>
                            <option value="<?= e((string)$v['id_veiculo']) ?>">
                                <?= e($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="prioridade">Prioridade Relatada</label>
                    <select class="form-control" name="prioridade" id="prioridade" required>
                        <option value="baixa">Baixa (Revisão, etc)</option>
                        <option value="media">Média (Barulhos, peças desgastadas)</option>
                        <option value="alta">Alta (O veículo não anda, vazamento grave)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="descricao_problema">Descrição Detalhada do Problema</label>
                    <textarea class="form-control" name="descricao_problema" id="descricao_problema" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Cadastrar Chamado</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../app/views/layouts/footer.php'; ?>
