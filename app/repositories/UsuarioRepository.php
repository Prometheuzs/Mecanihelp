<?php
declare(strict_types=1);

class UsuarioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function findByLogin(string $login): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE login = :login AND ativo = 1");
        $stmt->execute(['login' => $login]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public function autenticar(string $login, string $senha): ?array {
        $usuario = $this->findByLogin($login);
        if ($usuario && $usuario['senha'] === $senha) {
            return $usuario;
        }
        return null;
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT id_usuario, nome, login, perfil, ativo FROM usuario ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function inserir(array $dados): bool {
        $stmt = $this->db->prepare("INSERT INTO usuario (nome, login, senha, perfil, ativo) VALUES (:nome, :login, :senha, :perfil, 1)");
        try {
            $stmt->execute([
                'nome' => $dados['nome'],
                'login' => $dados['login'],
                'senha' => $dados['senha'],
                'perfil' => $dados['perfil']
            ]);
            return true;
        } catch (PDOException $e) {
            // Em caso de login duplicado, retorna falso.
            return false;
        }
    }
}
