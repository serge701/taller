<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Venta;

class ReporteController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();

        $filtros = [
            'fecha_inicio' => (string) $this->input('fecha_inicio'),
            'fecha_fin'    => (string) $this->input('fecha_fin'),
            'cliente'      => (string) $this->input('cliente'),
            'servicio'     => (string) $this->input('servicio'),
            'factura'      => (string) $this->input('factura'),
            'metodo_pago'  => (string) $this->input('metodo_pago'),
        ];

        $ventaModel = new Venta();
        $ventas     = $ventaModel->buscarReportes($filtros);

        $total    = count($ventas);
        $subtitulo = $total . ' ' . ($total === 1 ? 'venta encontrada' : 'ventas encontradas');
        if ($total >= Venta::REPORTE_LIMITE) {
            $subtitulo .= ' · se muestran las ' . Venta::REPORTE_LIMITE . ' más recientes, acota la búsqueda para ver menos';
        }

        $this->render('reportes/index', [
            'pageTitle'    => 'Reportes',
            'pageSubtitle' => $subtitulo,
            'ventas'       => $ventas,
            'filtros'      => $filtros,
            'clientes'     => $ventaModel->clientesConVenta(),
            'metodosPago'  => ['Efectivo', 'Tarjeta de Débito', 'Tarjeta de Crédito', 'Transferencia'],
        ]);
    }
}
