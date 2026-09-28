<?php
declare(strict_types=1);

class Auth {
    public static function login(array $usuario): void {
        session_regenerate_id(true);
        Session::set('usuario_id', $usuario['id_usuario']);
        Session::set('usuario_nome', $usuario['nome']);
        Session::set('usuario_perfil', $usuario['perfil']);
    }

    public static function logout(): void {
        Session::destroy();
    }

    public static function check(): bool {
        return Session::get('usuario_id') !== null;
    }

    public static function usuario(): ?array {
        if (!self::check()) return null;
        return [
            'id' => Session::get('usuario_id'),
            'nome' => Session::get('usuario_nome'),
            'perfil' => Session::get('usuario_perfil')
        ];
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            Flash::set('danger', 'Você precisa estar logado para acessar esta página.');
            redirect('/login.php');
        }
    }

    public static function requirePerfil(array $perfis): void {
        self::requireLogin();
        $usuario = self::usuario();
        if (!in_array($usuario['perfil'], $perfis, true)) {
            http_response_code(403);
            die("Acesso Negado (403). Perfil não autorizado.");
        }
    }
}
