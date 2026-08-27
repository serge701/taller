<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    public function edit(array $params): void
    {
        Auth::requireAdmin();
        $this->render('configuracion/index', [
            'pageTitle' => 'Configuración',
            'negocio'   => (new Configuracion())->obtener(),
        ]);
    }

    public function update(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $nombre = trim((string) $this->input('nombre_taller'));
        if ($nombre === '') {
            flash('error', 'El nombre del taller es obligatorio.');
            redirect('configuracion');
        }

        $rfc       = strtoupper(trim((string) $this->input('rfc')));
        $direccion = trim((string) $this->input('direccion'));
        $telefono  = trim((string) $this->input('telefono'));
        $email     = strtolower(trim((string) $this->input('email')));

        (new Configuracion())->actualizar([
            'nombre_taller' => $nombre,
            'rfc'           => $rfc !== '' ? $rfc : null,
            'direccion'     => $direccion !== '' ? $direccion : null,
            'telefono'      => $telefono !== '' ? $telefono : null,
            'email'         => $email !== '' ? $email : null,
        ]);

        flash('success', 'Configuración actualizada.');
        redirect('configuracion');
    }
}
