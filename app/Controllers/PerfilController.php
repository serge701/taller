<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Usuario;

class PerfilController extends Controller
{
    public function show(array $params): void
    {
        Auth::require();
        $usuario = (new Usuario())->find((int) Auth::id());
        $this->render('perfil/index', [
            'pageTitle' => 'Mi Perfil',
            'usuario'   => $usuario,
        ]);
    }

    public function update(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $nombre = trim((string) $this->input('nombre'));
        if ($nombre === '') {
            flash('error', 'El nombre es obligatorio.');
            redirect('perfil');
        }

        (new Usuario())->update((int) Auth::id(), ['nombre' => $nombre]);
        Auth::updateSession(['nombre' => $nombre]);

        flash('success', 'Perfil actualizado.');
        redirect('perfil');
    }

    public function password(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $actual     = (string) $this->input('password_actual');
        $nueva      = (string) $this->input('password_nueva');
        $confirmar  = (string) $this->input('password_confirmar');

        $model   = new Usuario();
        $usuario = $model->find((int) Auth::id());

        if (!$usuario || !password_verify($actual, $usuario['password'])) {
            flash('error', 'La contraseña actual no es correcta.');
            redirect('perfil');
        }
        if (strlen($nueva) < 6) {
            flash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            redirect('perfil');
        }
        if ($nueva !== $confirmar) {
            flash('error', 'La confirmación de contraseña no coincide.');
            redirect('perfil');
        }

        $model->updatePassword((int) Auth::id(), password_hash($nueva, PASSWORD_DEFAULT));

        flash('success', 'Contraseña actualizada correctamente.');
        redirect('perfil');
    }

    public function foto(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $resultado = procesar_subida_avatar($_FILES['foto'] ?? null, 'avatar_' . Auth::id());
        if (!$resultado['ok']) {
            flash('error', $resultado['error']);
            redirect('perfil');
        }

        $usuarioModel = new Usuario();
        $anterior     = $usuarioModel->find((int) Auth::id());
        eliminar_avatar_anterior($anterior['foto'] ?? null);

        $usuarioModel->update((int) Auth::id(), ['foto' => $resultado['archivo']]);
        Auth::updateSession(['foto' => $resultado['archivo']]);

        flash('success', 'Foto de perfil actualizada.');
        redirect('perfil');
    }
}
