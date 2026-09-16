<?php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ClienteController;
use App\Controllers\ServicioController;
use App\Controllers\VentaController;
use App\Controllers\UsuarioController;
use App\Controllers\PerfilController;
use App\Controllers\ReporteController;
use App\Controllers\TrabajoController;
use App\Controllers\ConfiguracionController;
use App\Controllers\VehiculoController;
use App\Controllers\VinController;
use App\Controllers\NotaPagoController;

return function (Router $router): void {

    // --- Auth ---
    $router->get('/login',   [AuthController::class, 'showLogin']);
    $router->post('/login',  [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // --- Dashboard ---
    $router->get('/',          [DashboardController::class, 'index']);
    $router->get('/dashboard', [DashboardController::class, 'index']);

    // --- Clientes ---
    $router->get('/clientes',                 [ClienteController::class, 'index']);
    $router->get('/clientes/buscar',          [ClienteController::class, 'buscar']);
    $router->get('/clientes/nuevo',           [ClienteController::class, 'create']);
    $router->post('/clientes',                [ClienteController::class, 'store']);
    $router->get('/clientes/{id}',            [ClienteController::class, 'show']);
    $router->get('/clientes/{id}/vehiculos',  [ClienteController::class, 'vehiculos']);
    $router->get('/clientes/{id}/editar',     [ClienteController::class, 'edit']);
    $router->post('/clientes/{id}',           [ClienteController::class, 'update']);
    $router->post('/clientes/{id}/eliminar',  [ClienteController::class, 'destroy']);

    // --- Servicios ---
    $router->get('/servicios',                [ServicioController::class, 'index']);
    $router->get('/servicios/buscar',         [ServicioController::class, 'buscar']);
    $router->get('/servicios/nuevo',          [ServicioController::class, 'create']);
    $router->post('/servicios',               [ServicioController::class, 'store']);
    $router->get('/servicios/{id}',           [ServicioController::class, 'show']);
    $router->get('/servicios/{id}/editar',    [ServicioController::class, 'edit']);
    $router->post('/servicios/{id}',          [ServicioController::class, 'update']);
    $router->post('/servicios/{id}/eliminar', [ServicioController::class, 'destroy']);

    // --- Trabajos (reparaciones largas que luego se cierran como Venta) ---
    $router->get('/trabajos',                     [TrabajoController::class, 'index']);
    $router->get('/trabajos/nuevo',                [TrabajoController::class, 'create']);
    $router->post('/trabajos',                     [TrabajoController::class, 'store']);
    $router->get('/trabajos/{id}',                 [TrabajoController::class, 'show']);
    $router->post('/trabajos/{id}/comentarios',    [TrabajoController::class, 'comentario']);
    $router->get('/trabajos/{id}/finalizar',       [TrabajoController::class, 'finalizar']);
    $router->post('/trabajos/{id}/finalizar',      [TrabajoController::class, 'finalizarStore']);
    $router->post('/trabajos/{id}/eliminar',       [TrabajoController::class, 'destroy']);

    // --- Ventas ---
    $router->get('/ventas',                   [VentaController::class, 'index']);
    $router->get('/ventas/nueva',             [VentaController::class, 'create']);
    $router->post('/ventas',                  [VentaController::class, 'store']);
    $router->get('/ventas/{id}',              [VentaController::class, 'show']);
    $router->post('/ventas/{id}/eliminar',    [VentaController::class, 'destroy']);

    // --- Usuarios (solo Admin) ---
    $router->get('/usuarios',                 [UsuarioController::class, 'index']);
    $router->get('/usuarios/nuevo',           [UsuarioController::class, 'create']);
    $router->post('/usuarios',                [UsuarioController::class, 'store']);
    $router->get('/usuarios/{id}/editar',     [UsuarioController::class, 'edit']);
    $router->post('/usuarios/{id}',           [UsuarioController::class, 'update']);
    $router->post('/usuarios/{id}/foto',      [UsuarioController::class, 'foto']);
    $router->post('/usuarios/{id}/eliminar',  [UsuarioController::class, 'destroy']);

    // --- Configuración del negocio (solo Admin) ---
    $router->get('/configuracion',  [ConfiguracionController::class, 'edit']);
    $router->post('/configuracion', [ConfiguracionController::class, 'update']);

    // --- Marcas y Modelos de vehículo (solo Admin) ---
    $router->get('/vehiculos',          [VehiculoController::class, 'index']);
    $router->post('/vehiculos/marcas',  [VehiculoController::class, 'storeMarca']);
    $router->post('/vehiculos/modelos', [VehiculoController::class, 'storeModelo']);

    // --- Notas de Pago (refacciones compradas a crédito, pendientes de pago) ---
    $router->get('/notas-pago',             [NotaPagoController::class, 'index']);
    $router->get('/notas-pago/nueva',       [NotaPagoController::class, 'create']);
    $router->post('/notas-pago',            [NotaPagoController::class, 'store']);
    $router->post('/notas-pago/{id}/pagar', [NotaPagoController::class, 'pagar']);
    $router->post('/notas-pago/{id}/eliminar', [NotaPagoController::class, 'destroy']);

    // --- Reportes ---
    $router->get('/reportes', [ReporteController::class, 'index']);

    // --- VIN (decodificador NHTSA vPIC, disponible para todos los roles) ---
    $router->get('/vin',           [VinController::class, 'index']);
    $router->get('/vin/buscar',    [VinController::class, 'buscar']);
    $router->get('/vin/consultas', [VinController::class, 'consultas']);

    // --- Perfil (usuario logueado) ---
    $router->get('/perfil',            [PerfilController::class, 'show']);
    $router->post('/perfil',           [PerfilController::class, 'update']);
    $router->post('/perfil/password',  [PerfilController::class, 'password']);
    $router->post('/perfil/foto',      [PerfilController::class, 'foto']);
};
