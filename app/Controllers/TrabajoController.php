<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Trabajo;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Venta;
use InvalidArgumentException;

class TrabajoController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();
        $trabajos = (new Trabajo())->todosConDetalle();
        $total = count($trabajos);
        $this->render('trabajos/index', [
            'pageTitle'    => 'Trabajos',
            'pageSubtitle' => $total . ' ' . ($total === 1 ? 'trabajo registrado' : 'trabajos registrados'),
            'pageActions'  => '<a href="' . url('trabajos/nuevo') . '" class="btn btn-primary btn-sm">
                                 <i class="bi bi-tools me-1"></i>Nuevo Trabajo
                               </a>',
            'trabajos'     => $trabajos,
        ]);
    }

    public function create(array $params): void
    {
        Auth::require();
        $this->render('trabajos/create', [
            'pageTitle' => 'Nuevo Trabajo',
            'servicios' => (new Servicio())->activos(),
        ]);
    }

    public function store(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $clienteId = (int) $this->input('cliente_id');
        $anticipo  = (float) str_replace(',', '', (string) $this->input('anticipo', '0'));
        $itemsRaw  = (string) $this->input('items');

        $vehiculo = [
            'marca'  => (string) $this->input('vehiculo_marca'),
            'color'  => (string) $this->input('vehiculo_color'),
            'placas' => (string) $this->input('vehiculo_placas'),
        ];

        if ($clienteId <= 0 || !(new Cliente())->find($clienteId)) {
            flash('error', 'Selecciona un cliente válido.');
            redirect('trabajos/nuevo');
        }

        $servicios = json_decode($itemsRaw, true);
        if (!is_array($servicios) || $servicios === []) {
            flash('error', 'Agrega al menos un servicio planeado.');
            redirect('trabajos/nuevo');
        }

        try {
            $trabajoId = (new Trabajo())->crearConServicios($clienteId, (int) Auth::id(), $vehiculo, $anticipo, $servicios);
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('trabajos/nuevo');
        }

        flash('success', 'Trabajo creado correctamente.');
        redirect('trabajos/' . $trabajoId);
    }

    public function show(array $params): void
    {
        Auth::require();
        $trabajoModel = new Trabajo();
        $trabajo = $trabajoModel->findConDetalle((int) $params['id']);
        if (!$trabajo) {
            flash('error', 'Trabajo no encontrado.');
            redirect('trabajos');
        }

        $this->render('trabajos/show', [
            'pageTitle'   => 'Trabajo #' . $trabajo['id'],
            'trabajo'     => $trabajo,
            'servicios'   => $trabajoModel->servicios((int) $trabajo['id']),
            'comentarios' => $trabajoModel->comentarios((int) $trabajo['id']),
        ]);
    }

    public function comentario(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $trabajoId = (int) $params['id'];
        $trabajoModel = new Trabajo();
        $trabajo = $trabajoModel->find($trabajoId);
        if (!$trabajo) {
            flash('error', 'Trabajo no encontrado.');
            redirect('trabajos');
        }
        if ($trabajo['estado'] !== 'Abierto') {
            flash('error', 'Este trabajo ya está cerrado.');
            redirect('trabajos/' . $trabajoId);
        }

        $texto = trim((string) $this->input('comentario'));
        if ($texto === '') {
            flash('error', 'Escribe un comentario.');
            redirect('trabajos/' . $trabajoId);
        }

        $trabajoModel->agregarComentario($trabajoId, (int) Auth::id(), $texto);
        flash('success', 'Comentario agregado.');
        redirect('trabajos/' . $trabajoId);
    }

    public function finalizar(array $params): void
    {
        Auth::require();
        $trabajoId = (int) $params['id'];
        $trabajoModel = new Trabajo();
        $trabajo = $trabajoModel->findConDetalle($trabajoId);
        if (!$trabajo) {
            flash('error', 'Trabajo no encontrado.');
            redirect('trabajos');
        }
        if ($trabajo['estado'] !== 'Abierto') {
            flash('error', 'Este trabajo ya está cerrado.');
            redirect('trabajos/' . $trabajoId);
        }

        $this->render('trabajos/finalizar', [
            'pageTitle'          => 'Finalizar Trabajo #' . $trabajo['id'],
            'trabajo'            => $trabajo,
            'serviciosPlaneados' => $trabajoModel->servicios($trabajoId),
            'servicios'          => (new Servicio())->activos(),
        ]);
    }

    public function finalizarStore(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $trabajoId = (int) $params['id'];
        $trabajoModel = new Trabajo();
        $trabajo = $trabajoModel->find($trabajoId);
        if (!$trabajo) {
            flash('error', 'Trabajo no encontrado.');
            redirect('trabajos');
        }
        if ($trabajo['estado'] !== 'Abierto') {
            flash('error', 'Este trabajo ya está cerrado.');
            redirect('trabajos/' . $trabajoId);
        }

        $metodoPago  = (string) $this->input('metodo_pago');
        $comentarios = (string) $this->input('comentarios');
        $itemsRaw    = (string) $this->input('items');
        $factura     = $this->input('factura', '') === '1';

        $metodosValidos = ['Efectivo', 'Tarjeta de Débito', 'Tarjeta de Crédito', 'Transferencia'];
        if (!in_array($metodoPago, $metodosValidos, true)) {
            flash('error', 'Selecciona un método de pago válido.');
            redirect('trabajos/' . $trabajoId . '/finalizar');
        }

        $items = json_decode($itemsRaw, true);
        if (!is_array($items) || $items === []) {
            flash('error', 'Agrega al menos un servicio a la venta.');
            redirect('trabajos/' . $trabajoId . '/finalizar');
        }

        $vehiculo = [
            'marca'  => $trabajo['vehiculo_marca'],
            'color'  => $trabajo['vehiculo_color'],
            'placas' => $trabajo['vehiculo_placas'],
        ];

        try {
            $ventaId = (new Venta())->crearConDetalle(
                (int) $trabajo['cliente_id'],
                (int) Auth::id(),
                $metodoPago,
                $comentarios,
                $items,
                $factura,
                $vehiculo
            );
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('trabajos/' . $trabajoId . '/finalizar');
        }

        $trabajoModel->cerrar($trabajoId, $ventaId);

        flash('success', 'Trabajo finalizado: se generó la venta #' . $ventaId . '.');
        redirect('ventas/' . $ventaId);
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        $trabajoId = (int) $params['id'];
        $trabajoModel = new Trabajo();
        $trabajo = $trabajoModel->find($trabajoId);

        if ($trabajo && $trabajo['estado'] !== 'Abierto') {
            flash('error', 'No se puede eliminar un trabajo ya cerrado.');
            redirect('trabajos');
        }

        $trabajoModel->delete($trabajoId);
        flash('success', 'Trabajo eliminado.');
        redirect('trabajos');
    }
}
