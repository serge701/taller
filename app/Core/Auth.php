<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\Usuario;

final class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $cfg = Config::get('session');

        session_name($cfg['name']);
        session_set_cookie_params([
            'lifetime' => $cfg['lifetime'],
            'path'     => Config::basePath() === '' ? '/' : Config::basePath(),
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        ]);

        session_start();
    }

    public static function attempt(string $usuario, string $password): bool
    {
        $model = new Usuario();
        $user  = $model->findByUsuario($usuario);

        if ($user === null || (int) ($user['activo'] ?? 1) !== 1) {
            return false;
        }

        if (!password_verify($password, $user['password'] ?? '')) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'      => (int) $user['id'],
            'nombre'  => $user['nombre'],
            'usuario' => $user['usuario'],
            'nivel'   => $user['nivel'],
            'foto'    => $user['foto'] ?? null,
        ];

        $model->touchLastLogin((int) $user['id']);

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']) ? (int) $_SESSION['user']['id'] : null;
    }

    public static function nivel(): ?string
    {
        return $_SESSION['user']['nivel'] ?? null;
    }

    public static function esAdmin(): bool
    {
        return self::nivel() === 'Admin';
    }

    public static function require(): void
    {
        if (!self::check()) {
            flash('error', 'Inicia sesión para continuar.');
            redirect('login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::require();
        if (!self::esAdmin()) {
            http_response_code(403);
            view('errors/403');
            exit;
        }
    }

    public static function updateSession(array $fields): void
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $fields);
        }
    }
}
