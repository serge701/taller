<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Trabajo;
use App\Models\ClienteVehiculo;

class ClienteController extends Controller
{
    /**
     * Vehículos guardados de un cliente (el más reciente primero), en JSON, para
     * prellenar el formulario de vehículo en /ventas/nueva y /trabajos/nuevo en
     * cuanto se selecciona ese cliente.
     */
    public function vehiculos(array $params): void
    {
        Auth::require();

        $lista = (new ClienteVehiculo())->porCliente((int) $params['id']);
        $datos = array_map(static function (array $v): array {
            return [
                'marca'   => $v['marca'],
                'modelo'  => $v['modelo'],
                'anio'    => $v['anio'],
                'color'   => $v['color'],
                'resumen' => vehiculo_resumen([
                    'vehiculo_marca'  => $v['marca'],
                    'vehiculo_modelo' => $v['modelo'],
                    'vehiculo_anio'   => $v['anio'],
                    'vehiculo_color'  => $v['color'],
                ]),
            ];
        }, $lista);

        $this->json($datos);
    }

    public function show(array $params): void
    {
        Auth::require();

        $cliente = (new Cliente())->find((int) $params['id']);
        if (!$cliente) {
            flash('error', 'Cliente no encontrado.');
            redirect('clientes');
        }

        $ventaModel = new Venta();
        $clienteId  = (int) $cliente['id'];

        // Mapeado a las mismas llaves que vehiculo_resumen() espera (vehiculo_marca, etc.)
        // para no tener que tocar la vista que ya está funcionando.
        $vehiculos = array_map(static function (array $v): array {
            return [
                'vehiculo_marca'  => $v['marca'],
                'vehiculo_modelo' => $v['modelo'],
                'vehiculo_anio'   => $v['anio'],
                'vehiculo_color'  => $v['color'],
                'ultima_vez'      => $v['updated_at'],
            ];
        }, (new ClienteVehiculo())->porCliente($clienteId));

        $this->render('clientes/show', [
            'pageTitle'         => trim($cliente['nombre'] . ' ' . $cliente['apellido_paterno']),
            'pageSubtitle'      => 'Ficha del cliente',
            'pageActions'       => '<a href="' . url('clientes/' . $clienteId . '/editar') . '" class="btn btn-outline-secondary btn-sm">
                                       <i class="bi bi-pencil me-1"></i>Editar
                                     </a>',
            'cliente'           => $cliente,
            'resumen'           => $ventaModel->resumenCliente($clienteId),
            'historial'         => $ventaModel->historialCliente($clienteId),
            'serviciosFrecuentes' => $ventaModel->serviciosFrecuentesCliente($clienteId),
            'vehiculos'         => $vehiculos,
            'trabajosAbiertos'  => (new Trabajo())->abiertosPorCliente($clienteId),
        ]);
    }

    public function index(array $params): void
    {
        Auth::require();
        $clientes = (new Cliente())->todosConVentas();
        $total = count($clientes);
        $this->render('clientes/index', [
            'pageTitle'    => 'Clientes',
            'pageSubtitle' => $total . ' ' . ($total === 1 ? 'cliente registrado' : 'clientes registrados'),
            'pageActions'  => '<a href="' . url('clientes/nuevo') . '" class="btn btn-primary btn-sm">
                                 <i class="bi bi-person-plus me-1"></i>Nuevo Cliente
                               </a>',
            'clientes'     => $clientes,
        ]);
    }

    public function create(array $params): void
    {
        Auth::require();
        $this->render('clientes/form', ['pageTitle' => 'Nuevo Cliente', 'cliente' => null]);
    }

    public function store(array $params): void
    {
        Auth::require();
        Csrf::verify();

        [$nombre, $apPat, $telefono, $email, $direccion, $error] = $this->validar();
        $fiscal = $this->datosFiscales();

        if ($error !== null) {
            set_old(array_merge([
                'nombre' => $nombre, 'apellido_paterno' => $apPat, 'telefono' => $telefono,
                'email' => $email, 'direccion' => $direccion,
            ], $fiscal));
            flash('error', $error);
            redirect('clientes/nuevo');
        }

        (new Cliente())->create(array_merge([
            'nombre'           => $nombre,
            'apellido_paterno' => $apPat,
            'telefono'         => $telefono,
            'email'            => $email !== '' ? $email : null,
            'direccion'        => $direccion !== '' ? $direccion : null,
        ], $fiscal));

        flash('success', 'Cliente agregado correctamente.');
        redirect('clientes');
    }

    public function edit(array $params): void
    {
        Auth::require();
        $cliente = (new Cliente())->find((int) $params['id']);
        if (!$cliente) {
            flash('error', 'Cliente no encontrado.');
            redirect('clientes');
        }
        $this->render('clientes/form', ['pageTitle' => 'Editar Cliente', 'cliente' => $cliente]);
    }

    public function update(array $params): void
    {
        Auth::require();
        Csrf::verify();

        [$nombre, $apPat, $telefono, $email, $direccion, $error] = $this->validar();
        $fiscal = $this->datosFiscales();

        if ($error !== null) {
            flash('error', $error);
            redirect('clientes/' . $params['id'] . '/editar');
        }

        (new Cliente())->update((int) $params['id'], array_merge([
            'nombre'           => $nombre,
            'apellido_paterno' => $apPat,
            'telefono'         => $telefono,
            'email'            => $email !== '' ? $email : null,
            'direccion'        => $direccion !== '' ? $direccion : null,
        ], $fiscal));

        flash('success', 'Cliente actualizado.');
        redirect('clientes');
    }

    public function destroy(array $params): void
    {
        Auth::requireAdmin();
        Csrf::verify();

        try {
            (new Cliente())->delete((int) $params['id']);
            flash('success', 'Cliente eliminado.');
        } catch (\PDOException $e) {
            flash('error', 'No se puede eliminar: el cliente tiene ventas registradas. Elimina primero sus ventas.');
        }

        redirect('clientes');
    }

    public function buscar(array $params): void
    {
        Auth::require();
        $q = (string) $this->input('q');
        $this->json($q !== '' ? (new Cliente())->buscar($q) : []);
    }

    private function validar(): array
    {
        $nombre    = ucwords(strtolower(trim((string) $this->input('nombre'))));
        $apPat     = ucwords(strtolower(trim((string) $this->input('apellido_paterno'))));
        $telefono  = trim((string) $this->input('telefono'));
        $email     = strtolower(trim((string) $this->input('email')));
        $direccion = trim((string) $this->input('direccion'));

        $error = null;
        if ($nombre === '' || $apPat === '' || $telefono === '') {
            $error = 'Nombre, Apellido Paterno y Teléfono son obligatorios.';
        }

        return [$nombre, $apPat, $telefono, $email, $direccion, $error];
    }

    /**
     * Datos fiscales opcionales (para clientes que requieren factura en sus ventas).
     */
    private function datosFiscales(): array
    {
        $rfc          = strtoupper(trim((string) $this->input('rfc')));
        $razonSocial  = trim((string) $this->input('razon_social'));
        $regimenFiscal = trim((string) $this->input('regimen_fiscal'));
        $cpFiscal     = trim((string) $this->input('cp_fiscal'));

        return [
            'rfc'            => $rfc !== '' ? $rfc : null,
            'razon_social'   => $razonSocial !== '' ? $razonSocial : null,
            'regimen_fiscal' => $regimenFiscal !== '' ? $regimenFiscal : null,
            'cp_fiscal'      => $cpFiscal !== '' ? $cpFiscal : null,
        ];
    }
}
