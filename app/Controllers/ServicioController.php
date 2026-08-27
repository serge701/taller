<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();
        $servicios = (new Servicio())->all([], 'nombre ASC', 1000);
        $total = count($servicios);
        $this->render('servicios/index', [
            'pageTitle'    => 'Servicios',
            'pageSubtitle' => $total . ' ' . ($total === 1 ? 'servicio registrado' : 'servicios registrados'),
            'pageActions'  => '<a href="' . url('servicios/nuevo') . '" class="btn btn-primary btn-sm">
                                 <i class="bi bi-plus-circle me-1"></i>Nuevo Servicio
                               </a>',
            'servicios'    => $servicios,
        ]);
    }

    public function create(array $params): void
    {
        Auth::require();
        $this->render('servicios/form', ['pageTitle' => 'Nuevo Servicio', 'servicio' => null]);
    }

    public function store(array $params): void
    {
        Auth::require();
        Csrf::verify();

        [$nombre, $precio, $error] = $this->validar();
        if ($error !== null) {
            set_old(['nombre' => $nombre, 'precio' => (string) $precio]);
            flash('error', $error);
            redirect('servicios/nuevo');
        }

        (new Servicio())->create([
            'nombre' => $nombre,
            'precio' => $precio,
            'activo' => 1,
        ]);

        flash('success', 'Servicio agregado correctamente.');
        redirect('servicios');
    }

    public function edit(array $params): void
    {
        Auth::require();
        $servicio = (new Servicio())->find((int) $params['id']);
        if (!$servicio) {
            flash('error', 'Servicio no encontrado.');
            redirect('servicios');
        }
        $this->render('servicios/form', ['pageTitle' => 'Editar Servicio', 'servicio' => $servicio]);
    }

    public function update(array $params): void
    {
        Auth::require();
        Csrf::verify();

        [$nombre, $precio, $error] = $this->validar();
        if ($error !== null) {
            flash('error', $error);
            redirect('servicios/' . $params['id'] . '/editar');
        }

        (new Servicio())->update((int) $params['id'], [
            'nombre' => $nombre,
            'precio' => $precio,
            'activo' => (int) $this->input('activo', 0),
        ]);

        flash('success', 'Servicio actualizado.');
        redirect('servicios');
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        (new Servicio())->delete((int) $params['id']);
        flash('success', 'Servicio eliminado.');
        redirect('servicios');
    }

    public function buscar(array $params): void
    {
        Auth::require();
        $q = (string) $this->input('q');
        $this->json($q !== '' ? (new Servicio())->buscar($q) : (new Servicio())->activos());
    }

    private function validar(): array
    {
        $nombre = trim((string) $this->input('nombre'));
        $precio = (float) str_replace(',', '', (string) $this->input('precio'));

        $error = null;
        if ($nombre === '' || $precio <= 0) {
            $error = 'Nombre del servicio y precio (mayor a 0) son obligatorios.';
        }

        return [$nombre, round($precio, 2), $error];
    }
}
