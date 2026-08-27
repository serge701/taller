<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Usuario;

class PerfilController extends Controller
{
    private const FOTO_DIR = BASE_DIR . '/assets/img/avatars';
    private const FOTO_MAX_BYTES = 3 * 1024 * 1024; // 3MB
    private const FOTO_TAMANO = 400; // px, imagen cuadrada final

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

        $file = $_FILES['foto'] ?? null;
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            flash('error', 'Selecciona una imagen.');
            redirect('perfil');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Ocurrió un error al subir la imagen.');
            redirect('perfil');
        }
        if ($file['size'] > self::FOTO_MAX_BYTES) {
            flash('error', 'La imagen no debe superar 3MB.');
            redirect('perfil');
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            flash('error', 'El archivo no es una imagen válida.');
            redirect('perfil');
        }

        $origen = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => @imagecreatefrompng($file['tmp_name']),
            'image/webp' => @imagecreatefromwebp($file['tmp_name']),
            default      => null,
        };

        if ($origen === false || $origen === null) {
            flash('error', 'Formato de imagen no soportado. Usa JPG, PNG o WEBP.');
            redirect('perfil');
        }

        if (!is_dir(self::FOTO_DIR)) {
            mkdir(self::FOTO_DIR, 0755, true);
        }

        $nombreArchivo = 'avatar_' . Auth::id() . '_' . time() . '.jpg';
        $destino       = self::FOTO_DIR . '/' . $nombreArchivo;

        $this->guardarComoCuadrada($origen, $destino, self::FOTO_TAMANO);

        $usuarioModel = new Usuario();
        $anterior     = $usuarioModel->find((int) Auth::id());
        if (!empty($anterior['foto'])) {
            $rutaAnterior = self::FOTO_DIR . '/' . $anterior['foto'];
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $usuarioModel->update((int) Auth::id(), ['foto' => $nombreArchivo]);
        Auth::updateSession(['foto' => $nombreArchivo]);

        flash('success', 'Foto de perfil actualizada.');
        redirect('perfil');
    }

    /**
     * Recorta al centro y redimensiona la imagen de origen a un cuadrado de $size px,
     * guardándola como JPG en $destino. Libera ambos recursos GD al terminar.
     */
    private function guardarComoCuadrada(\GdImage $origen, string $destino, int $size): void
    {
        $anchoOrig = imagesx($origen);
        $altoOrig  = imagesy($origen);
        $lado      = min($anchoOrig, $altoOrig);
        $srcX      = intdiv($anchoOrig - $lado, 2);
        $srcY      = intdiv($altoOrig - $lado, 2);

        $destinoImg = imagecreatetruecolor($size, $size);
        imagecopyresampled($destinoImg, $origen, 0, 0, $srcX, $srcY, $size, $size, $lado, $lado);
        imagejpeg($destinoImg, $destino, 85);

        imagedestroy($destinoImg);
        imagedestroy($origen);
    }
}
