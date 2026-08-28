<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\VehiculoMarca;
use InvalidArgumentException;

class VehiculoController extends Controller
{
    public function index(array $params): void
    {
        Auth::requireAdmin();

        $marcas = (new VehiculoMarca())->todasConModelos();
        $totalModelos = array_sum(array_map(static fn($m) => count($m['modelos']), $marcas));

        $this->render('vehiculos/index', [
            'pageTitle'    => 'Marcas y Modelos',
            'pageSubtitle' => count($marcas) . ' marcas · ' . $totalModelos . ' modelos',
            'marcas'       => $marcas,
        ]);
    }

    public function storeMarca(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $nombre = trim((string) $this->input('nombre'));
        if ($nombre === '') {
            flash('error', 'Escribe el nombre de la marca.');
            redirect('vehiculos');
        }

        try {
            (new VehiculoMarca())->crearMarca($nombre);
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('vehiculos');
        }

        flash('success', 'Marca agregada correctamente.');
        redirect('vehiculos');
    }

    public function storeModelo(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $marcaId = (int) $this->input('marca_id');
        $nombre  = trim((string) $this->input('nombre'));

        if ($marcaId <= 0 || $nombre === '') {
            flash('error', 'Selecciona la marca y escribe el nombre del modelo.');
            redirect('vehiculos');
        }

        try {
            (new VehiculoMarca())->crearModelo($marcaId, $nombre);
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('vehiculos');
        }

        flash('success', 'Modelo agregado correctamente.');
        redirect('vehiculos');
    }
}
