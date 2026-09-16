<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\NotaPago;
use App\Models\Trabajo;

class NotaPagoController extends Controller
{
    public function index(array $params): void
    {
        Auth::require();

        $notaModel = new NotaPago();
        $notas     = $notaModel->todasConDetalle();
        $total     = count($notas);

        $this->render('notas_pago/index', [
            'pageTitle'      => 'Notas de Pago',
            'pageSubtitle'   => $total . ' ' . ($total === 1 ? 'nota registrada' : 'notas registradas'),
            'pageActions'    => '<a href="' . url('notas-pago/nueva') . '" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Nueva Nota
                                  </a>',
            'notas'          => $notas,
            'totalPendiente' => $notaModel->totalPendiente(),
        ]);
    }

    public function create(array $params): void
    {
        Auth::require();
        $this->render('notas_pago/form', [
            'pageTitle' => 'Nueva Nota de Pago',
            'trabajos'  => (new Trabajo())->todosConDetalle(),
        ]);
    }

    public function store(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $monto       = (float) str_replace(',', '', (string) $this->input('monto'));
        $descripcion = trim((string) $this->input('descripcion'));
        $trabajoId   = (int) $this->input('trabajo_id', 0);

        if ($monto <= 0 || $descripcion === '') {
            set_old([
                'monto'       => (string) $monto,
                'descripcion' => $descripcion,
                'trabajo_id'  => (string) $trabajoId,
            ]);
            flash('error', 'El monto (mayor a 0) y la descripción son obligatorios.');
            redirect('notas-pago/nueva');
        }

        (new NotaPago())->create([
            'monto'       => round($monto, 2),
            'descripcion' => $descripcion,
            'trabajo_id'  => $trabajoId > 0 ? $trabajoId : null,
            'usuario_id'  => Auth::id(),
            'estado'      => 'Pendiente',
        ]);

        flash('success', 'Nota de pago registrada.');
        redirect('notas-pago');
    }

    public function pagar(array $params): void
    {
        Auth::require();
        Csrf::verify();

        $notaModel = new NotaPago();
        $nota      = $notaModel->find((int) $params['id']);
        if (!$nota) {
            flash('error', 'Nota de pago no encontrada.');
            redirect('notas-pago');
        }

        $comentarios = trim((string) $this->input('comentarios'));
        $notaModel->marcarPagada((int) $params['id'], $comentarios !== '' ? $comentarios : null);

        flash('success', 'Nota de pago marcada como pagada.');
        redirect('notas-pago');
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        (new NotaPago())->delete((int) $params['id']);
        flash('success', 'Nota de pago eliminada.');
        redirect('notas-pago');
    }
}
