<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function index(array $params): void
    {
        Auth::requireAdmin();
        $usuarios = (new Usuario())->all([], 'nombre ASC');
        $this->render('usuarios/index', [
            'pageTitle'    => 'Usuarios del Sistema',
            'pageSubtitle' => count($usuarios) . ' usuarios registrados',
            'pageActions'  => '<a href="' . url('usuarios/nuevo') . '" class="btn btn-primary btn-sm">
                                 <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                               </a>',
            'usuarios'     => $usuarios,
        ]);
    }

    public function create(array $params): void
    {
        Auth::requireAdmin();
        $this->render('usuarios/form', ['pageTitle' => 'Nuevo Usuario', 'usuario' => null]);
    }

    public function store(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $usuarioLogin = trim((string) $this->input('usuario'));
        $nombre       = trim((string) $this->input('nombre'));
        $nivel        = $this->input('nivel', 'Usuario') === 'Admin' ? 'Admin' : 'Usuario';
        $password     = (string) $this->input('password');

        if ($usuarioLogin === '' || $nombre === '') {
            flash('error', 'Usuario y nombre son obligatorios.');
            redirect('usuarios/nuevo');
        }

        if (strlen($password) < 6) {
            flash('error', 'La contraseña debe tener al menos 6 caracteres.');
            redirect('usuarios/nuevo');
        }

        if ((new Usuario())->findByUsuario($usuarioLogin)) {
            flash('error', 'Ese nombre de usuario ya existe.');
            redirect('usuarios/nuevo');
        }

        (new Usuario())->create([
            'usuario'  => $usuarioLogin,
            'nombre'   => $nombre,
            'nivel'    => $nivel,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'activo'   => 1,
        ]);

        flash('success', 'Usuario creado.');
        redirect('usuarios');
    }

    public function edit(array $params): void
    {
        Auth::requireAdmin();
        $usuario = (new Usuario())->find((int) $params['id']);
        if (!$usuario) {
            flash('error', 'Usuario no encontrado.');
            redirect('usuarios');
        }
        $this->render('usuarios/form', ['pageTitle' => 'Editar Usuario', 'usuario' => $usuario]);
    }

    public function update(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $nombre = trim((string) $this->input('nombre'));
        $nivel  = $this->input('nivel') === 'Admin' ? 'Admin' : 'Usuario';

        if ($nombre === '') {
            flash('error', 'El nombre es obligatorio.');
            redirect('usuarios/' . $params['id'] . '/editar');
        }

        $data = [
            'nombre' => $nombre,
            'nivel'  => $nivel,
            'activo' => (int) $this->input('activo', 0),
        ];

        $password = (string) $this->input('password');
        if ($password !== '') {
            if (strlen($password) < 6) {
                flash('error', 'La contraseña debe tener al menos 6 caracteres.');
                redirect('usuarios/' . $params['id'] . '/editar');
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        (new Usuario())->update((int) $params['id'], $data);
        flash('success', 'Usuario actualizado.');
        redirect('usuarios');
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        if ((int) $params['id'] === Auth::id()) {
            flash('error', 'No puedes eliminar tu propio usuario.');
            redirect('usuarios');
        }

        try {
            (new Usuario())->delete((int) $params['id']);
            flash('success', 'Usuario eliminado.');
        } catch (\PDOException $e) {
            flash('error', 'No se puede eliminar: el usuario tiene ventas registradas a su nombre.');
        }

        redirect('usuarios');
    }
}
