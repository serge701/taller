<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Venta;
use App\Models\Trabajo;
use App\Models\NotaPago;

class DashboardController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();

        $ventaModel = new Venta();
        $anioActual = (int) date('Y');

        $mes         = $ventaModel->estadisticasMes();
        $mesAnterior = $ventaModel->estadisticasMesAnterior();

        $cambioMes = null;
        if ((float) $mesAnterior['total'] > 0) {
            $cambioMes = (((float) $mes['total'] - (float) $mesAnterior['total']) / (float) $mesAnterior['total']) * 100;
        } elseif ((float) $mes['total'] > 0) {
            $cambioMes = 100.0;
        }

        $this->render('dashboard/index', [
            'pageTitle'       => 'Dashboard',
            'totalClientes'   => count((new Cliente())->all([], 'id DESC', 100000)),
            'totalServicios'  => count((new Servicio())->activos()),
            'hoy'             => $ventaModel->estadisticasHoy(),
            'semana'          => $ventaModel->estadisticasSemana(),
            'mes'             => $mes,
            'mesAnterior'     => $mesAnterior,
            'cambioMes'       => $cambioMes,
            'anio'            => $ventaModel->estadisticasAnio($anioActual),
            'anioActual'      => $anioActual,
            'ventasPorDia'    => $ventaModel->ventasPorDia(14),
            'ventasPorMes'    => $ventaModel->ventasPorMes($anioActual),
            'distribucionPago'=> $ventaModel->distribucionMetodoPago(),
            'topServicios'    => $ventaModel->topServicios(5),
            'ultimasVentas'   => array_slice($ventaModel->todasConDetalle(), 0, 10),
            'trabajosAbiertos'=> (new Trabajo())->estadisticasAbiertos(),
            'trabajosEnCurso' => (new Trabajo())->abiertosOrdenados(6),
            'notasPagoPendientes' => (new NotaPago())->estadisticasPendientes(),
            'notasPagoLista'      => (new NotaPago())->pendientesOrdenadas(6),
        ]);
    }
}
