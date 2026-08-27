<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Servicio;
use InvalidArgumentException;

class VentaController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();
        $ventas = (new Venta())->todasConDetalle();
        $total = count($ventas);
        $this->render('ventas/index', [
            'pageTitle'    => 'Ventas',
            'pageSubtitle' => $total . ' ' . ($total === 1 ? 'venta registrada' : 'ventas registradas'),
            'pageActions'  => '<a href="' . url('ventas/nueva') . '" class="btn btn-primary btn-sm">
                                 <i class="bi bi-cart-plus me-1"></i>Nueva Venta
                               </a>',
            'ventas'       => $ventas,
        ]);
    }

    public function create(array $params): void
    {
        Auth::require();
        $this->render('ventas/create', [
            'pageTitle' => 'Nueva Venta',
            'servicios' => (new Servicio())->activos(),
        ]);
    }

    public function store(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $clienteId   = (int) $this->input('cliente_id');
        $metodoPago  = (string) $this->input('metodo_pago');
        $comentarios = (string) $this->input('comentarios');
        $itemsRaw    = (string) $this->input('items');
        $factura     = $this->input('factura', '') === '1';

        $vehiculo = [
            'marca'  => (string) $this->input('vehiculo_marca'),
            'color'  => (string) $this->input('vehiculo_color'),
            'placas' => (string) $this->input('vehiculo_placas'),
        ];

        $metodosValidos = ['Efectivo', 'Tarjeta de Débito', 'Tarjeta de Crédito', 'Transferencia'];

        if ($clienteId <= 0 || !(new Cliente())->find($clienteId)) {
            flash('error', 'Selecciona un cliente válido.');
            redirect('ventas/nueva');
        }

        if (!in_array($metodoPago, $metodosValidos, true)) {
            flash('error', 'Selecciona un método de pago válido.');
            redirect('ventas/nueva');
        }

        $items = json_decode($itemsRaw, true);
        if (!is_array($items) || $items === []) {
            flash('error', 'Agrega al menos un servicio a la venta.');
            redirect('ventas/nueva');
        }

        try {
            $ventaId = (new Venta())->crearConDetalle(
                $clienteId,
                (int) Auth::id(),
                $metodoPago,
                $comentarios,
                $items,
                $factura,
                $vehiculo
            );
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('ventas/nueva');
        }

        flash('success', 'Venta registrada correctamente.');
        redirect('ventas/' . $ventaId);
    }

    public function show(array $params): void
    {
        Auth::require();
        $ventaModel = new Venta();
        $venta = $ventaModel->findConDetalle((int) $params['id']);
        if (!$venta) {
            flash('error', 'Venta no encontrada.');
            redirect('ventas');
        }

        $this->render('ventas/show', [
            'pageTitle' => 'Venta #' . $venta['id'],
            'venta'     => $venta,
            'items'     => $ventaModel->items((int) $venta['id']),
        ]);
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        (new Venta())->delete((int) $params['id']);
        flash('success', 'Venta eliminada.');
        redirect('ventas');
    }
}
