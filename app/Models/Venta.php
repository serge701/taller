<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use InvalidArgumentException;

class Venta extends BaseModel
{
    protected string $table    = 'ventas';
    protected array  $fillable = [
        'cliente_id', 'usuario_id', 'metodo_pago', 'comentarios',
        'factura', 'subtotal', 'iva', 'total',
        'vehiculo_marca', 'vehiculo_modelo', 'vehiculo_anio', 'vehiculo_color',
    ];

    public const IVA_TASA = 0.16;

    /**
     * Listado de ventas con datos de cliente y usuario para la tabla índice.
     */
    public function todasConDetalle(): array
    {
        $stmt = $this->db->query(
            "SELECT v.*,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    u.nombre AS usuario_nombre,
                    t.id AS trabajo_id
             FROM ventas v
             INNER JOIN clientes c ON c.id = v.cliente_id
             INNER JOIN users u ON u.id = v.usuario_id
             LEFT JOIN trabajos t ON t.venta_id = v.id
             ORDER BY v.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function findConDetalle(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    c.telefono AS cliente_telefono, c.email AS cliente_email, c.direccion AS cliente_direccion,
                    c.rfc AS cliente_rfc, c.razon_social AS cliente_razon_social,
                    c.regimen_fiscal AS cliente_regimen_fiscal, c.cp_fiscal AS cliente_cp_fiscal,
                    u.nombre AS usuario_nombre,
                    t.id AS trabajo_id
             FROM ventas v
             INNER JOIN clientes c ON c.id = v.cliente_id
             INNER JOIN users u ON u.id = v.usuario_id
             LEFT JOIN trabajos t ON t.venta_id = v.id
             WHERE v.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function items(int $ventaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM venta_detalle WHERE venta_id = :id ORDER BY id ASC"
        );
        $stmt->execute(['id' => $ventaId]);
        return $stmt->fetchAll();
    }

    /**
     * Crea una venta junto con sus líneas de detalle dentro de una transacción.
     * $items = [['servicio_id' => ?int, 'nombre' => ?string, 'cantidad' => int, 'precio' => ?float], ...]
     * 'servicio_id' es opcional (p. ej. al cerrar un Trabajo cuyo servicio planeado ya no
     * existe en el catálogo); en ese caso se requiere 'nombre' y 'precio' explícitos.
     * $vehiculo = ['marca' => string, 'modelo' => string, 'anio' => int|string, 'color' => string]
     */
    public function crearConDetalle(
        int $clienteId,
        int $usuarioId,
        string $metodoPago,
        string $comentarios,
        array $items,
        bool $factura,
        array $vehiculo
    ): int {
        if ($items === []) {
            throw new InvalidArgumentException('La venta debe tener al menos un servicio.');
        }

        $anio = (int) ($vehiculo['anio'] ?? 0);
        if (
            trim((string) ($vehiculo['marca'] ?? '')) === ''
            || trim((string) ($vehiculo['modelo'] ?? '')) === ''
            || $anio <= 0
            || trim((string) ($vehiculo['color'] ?? '')) === ''
        ) {
            throw new InvalidArgumentException('Marca, modelo, año y color del vehículo son obligatorios.');
        }

        $servicioModel = new Servicio();

        $this->db->beginTransaction();
        try {
            $subtotal = 0.0;
            $lineas = [];

            foreach ($items as $item) {
                $servicioId = !empty($item['servicio_id']) ? (int) $item['servicio_id'] : null;
                $servicio   = $servicioId ? $servicioModel->find($servicioId) : null;

                // El servicio pudo haberse eliminado del catálogo desde que se planeó
                // (p. ej. en un Trabajo abierto hace tiempo): en ese caso se conserva
                // el nombre que venga en el item y la línea queda sin servicio_id.
                if ($servicioId && !$servicio) {
                    $servicioId = null;
                }
                $nombre = $servicio['nombre'] ?? trim((string) ($item['nombre'] ?? ''));
                if ($nombre === '') {
                    throw new InvalidArgumentException('Hay un servicio sin nombre en la venta.');
                }

                $cantidad = max(1, (int) $item['cantidad']);

                // El precio de catálogo es solo el punto de partida: el precio final
                // por línea es editable (muchos servicios varían según el vehículo).
                $precio = isset($item['precio']) ? round((float) $item['precio'], 2) : (float) ($servicio['precio'] ?? 0);
                if ($precio <= 0) {
                    throw new InvalidArgumentException('El precio de "' . $nombre . '" debe ser mayor a 0.');
                }

                $subtotalLinea = round($precio * $cantidad, 2);
                $subtotal    += $subtotalLinea;

                $lineas[] = [
                    'servicio_id'     => $servicioId,
                    'nombre_servicio' => $nombre,
                    'precio'          => $precio,
                    'cantidad'        => $cantidad,
                    'subtotal'        => $subtotalLinea,
                ];
            }

            $subtotal = round($subtotal, 2);
            $iva      = $factura ? round($subtotal * self::IVA_TASA, 2) : 0.0;
            $total    = round($subtotal + $iva, 2);

            $ventaId = $this->create([
                'cliente_id'      => $clienteId,
                'usuario_id'      => $usuarioId,
                'metodo_pago'     => $metodoPago,
                'comentarios'     => $comentarios !== '' ? $comentarios : null,
                'factura'         => $factura ? 1 : 0,
                'subtotal'        => $subtotal,
                'iva'             => $iva,
                'total'           => $total,
                'vehiculo_marca'  => trim((string) $vehiculo['marca']),
                'vehiculo_modelo' => trim((string) $vehiculo['modelo']),
                'vehiculo_anio'   => $anio,
                'vehiculo_color'  => trim((string) $vehiculo['color']),
            ]);

            $stmt = $this->db->prepare(
                "INSERT INTO venta_detalle (venta_id, servicio_id, nombre_servicio, precio, cantidad, subtotal)
                 VALUES (:venta_id, :servicio_id, :nombre_servicio, :precio, :cantidad, :subtotal)"
            );
            foreach ($lineas as $l) {
                $stmt->execute([
                    'venta_id'        => $ventaId,
                    'servicio_id'     => $l['servicio_id'],
                    'nombre_servicio' => $l['nombre_servicio'],
                    'precio'          => $l['precio'],
                    'cantidad'        => $l['cantidad'],
                    'subtotal'        => $l['subtotal'],
                ]);
            }

            $this->db->commit();
            return $ventaId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function estadisticasHoy(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas WHERE DATE(created_at) = CURDATE()"
        );
        return $stmt->fetch() ?: ['ventas' => 0, 'total' => 0];
    }

    public function estadisticasMes(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())"
        );
        return $stmt->fetch() ?: ['ventas' => 0, 'total' => 0];
    }

    public function estadisticasSemana(): array
    {
        // Semana ISO (lunes a domingo) que contiene hoy.
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"
        );
        return $stmt->fetch() ?: ['ventas' => 0, 'total' => 0];
    }

    public function estadisticasMesAnterior(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas
             WHERE YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)
               AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)"
        );
        return $stmt->fetch() ?: ['ventas' => 0, 'total' => 0];
    }

    public function estadisticasAnio(?int $anio = null): array
    {
        $anio = $anio ?? (int) date('Y');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas WHERE YEAR(created_at) = :anio"
        );
        $stmt->execute(['anio' => $anio]);
        return $stmt->fetch() ?: ['ventas' => 0, 'total' => 0];
    }

    /**
     * Total vendido por día en los últimos $dias días (incluye días sin ventas en $0).
     * Devuelve un arreglo ordenado cronológicamente: ['2026-08-01' => ['ventas'=>2,'total'=>150.00], ...]
     */
    public function ventasPorDia(int $dias = 14): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS dia, COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas
             WHERE created_at >= (CURDATE() - INTERVAL :dias DAY)
             GROUP BY DATE(created_at)"
        );
        $stmt->bindValue('dias', $dias - 1, \PDO::PARAM_INT);
        $stmt->execute();
        $filas = [];
        foreach ($stmt->fetchAll() as $r) {
            $filas[$r['dia']] = ['ventas' => (int) $r['ventas'], 'total' => (float) $r['total']];
        }

        $resultado = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));
            $resultado[$fecha] = $filas[$fecha] ?? ['ventas' => 0, 'total' => 0.0];
        }
        return $resultado;
    }

    /**
     * Total vendido por mes del año dado (12 posiciones, meses sin ventas en $0).
     */
    public function ventasPorMes(?int $anio = null): array
    {
        $anio = $anio ?? (int) date('Y');
        $stmt = $this->db->prepare(
            "SELECT MONTH(created_at) AS mes, COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas WHERE YEAR(created_at) = :anio
             GROUP BY MONTH(created_at)"
        );
        $stmt->execute(['anio' => $anio]);
        $filas = [];
        foreach ($stmt->fetchAll() as $r) {
            $filas[(int) $r['mes']] = ['ventas' => (int) $r['ventas'], 'total' => (float) $r['total']];
        }

        $resultado = [];
        for ($m = 1; $m <= 12; $m++) {
            $resultado[$m] = $filas[$m] ?? ['ventas' => 0, 'total' => 0.0];
        }
        return $resultado;
    }

    public function distribucionMetodoPago(): array
    {
        $stmt = $this->db->query(
            "SELECT metodo_pago, COUNT(*) AS ventas, COALESCE(SUM(total),0) AS total
             FROM ventas GROUP BY metodo_pago"
        );
        return $stmt->fetchAll();
    }

    public function topServicios(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT nombre_servicio, SUM(cantidad) AS cantidad, SUM(subtotal) AS total
             FROM venta_detalle
             GROUP BY nombre_servicio
             ORDER BY total DESC
             LIMIT :limite"
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public const REPORTE_LIMITE = 200;

    /**
     * KPIs de un cliente para su ficha 360° en /clientes/{id}.
     */
    public function resumenCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total_ventas,
                    COALESCE(SUM(total), 0) AS total_gastado,
                    MAX(created_at) AS ultima_visita
             FROM ventas WHERE cliente_id = :id"
        );
        $stmt->execute(['id' => $clienteId]);
        return $stmt->fetch() ?: ['total_ventas' => 0, 'total_gastado' => 0, 'ultima_visita' => null];
    }

    /**
     * Historial completo de ventas de un cliente (más recientes primero), para su ficha 360°.
     */
    public function historialCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*,
                    (SELECT GROUP_CONCAT(nombre_servicio SEPARATOR ', ') FROM venta_detalle vd WHERE vd.venta_id = v.id) AS servicios
             FROM ventas v
             WHERE v.cliente_id = :id
             ORDER BY v.created_at DESC"
        );
        $stmt->execute(['id' => $clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Servicios que más ha pedido un cliente, para su ficha 360°.
     */
    public function serviciosFrecuentesCliente(int $clienteId, int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT vd.nombre_servicio, SUM(vd.cantidad) AS veces, SUM(vd.subtotal) AS total
             FROM venta_detalle vd
             INNER JOIN ventas v ON v.id = vd.venta_id
             WHERE v.cliente_id = :id
             GROUP BY vd.nombre_servicio
             ORDER BY veces DESC, total DESC
             LIMIT :limite"
        );
        $stmt->bindValue('id', $clienteId, \PDO::PARAM_INT);
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * KPIs de un servicio para su página de historial en /servicios/{id}.
     */
    public function resumenServicio(int $servicioId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS veces_vendido,
                    COALESCE(SUM(vd.cantidad), 0) AS unidades,
                    COALESCE(SUM(vd.subtotal), 0) AS total_generado,
                    MAX(v.created_at) AS ultima_venta
             FROM venta_detalle vd
             INNER JOIN ventas v ON v.id = vd.venta_id
             WHERE vd.servicio_id = :id"
        );
        $stmt->execute(['id' => $servicioId]);
        return $stmt->fetch() ?: ['veces_vendido' => 0, 'unidades' => 0, 'total_generado' => 0, 'ultima_venta' => null];
    }

    /**
     * Historial de ventas donde se usó un servicio (más recientes primero), para
     * su página de historial en /servicios/{id}.
     */
    public function historialServicio(int $servicioId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.id, v.created_at, v.metodo_pago, v.total AS total_venta,
                    vd.cantidad, vd.precio, vd.subtotal,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    v.vehiculo_marca, v.vehiculo_modelo, v.vehiculo_anio, v.vehiculo_color
             FROM venta_detalle vd
             INNER JOIN ventas v ON v.id = vd.venta_id
             INNER JOIN clientes c ON c.id = v.cliente_id
             WHERE vd.servicio_id = :id
             ORDER BY v.created_at DESC"
        );
        $stmt->execute(['id' => $servicioId]);
        return $stmt->fetchAll();
    }

    /**
     * Clientes distintos que tienen al menos una venta registrada, para el selector
     * de la página de Reportes (evita listar clientes que nunca han comprado nada).
     */
    public function clientesConVenta(): array
    {
        $stmt = $this->db->query(
            "SELECT DISTINCT c.id, c.nombre, c.apellido_paterno
             FROM clientes c
             INNER JOIN ventas v ON v.cliente_id = c.id
             ORDER BY c.nombre ASC, c.apellido_paterno ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Búsqueda de ventas para la página de Reportes.
     * $filtros acepta: fecha_inicio, fecha_fin, cliente (id del cliente), servicio, factura ('0'|'1'|''), metodo_pago.
     * Devuelve como máximo self::REPORTE_LIMITE resultados, los más recientes primero.
     */
    public function buscarReportes(array $filtros): array
    {
        $where  = [];
        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'DATE(v.created_at) >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'DATE(v.created_at) <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        if (!empty($filtros['cliente'])) {
            $where[] = 'v.cliente_id = :cliente';
            $params['cliente'] = (int) $filtros['cliente'];
        }
        if (!empty($filtros['servicio'])) {
            $where[] = 'EXISTS (SELECT 1 FROM venta_detalle vd WHERE vd.venta_id = v.id AND vd.nombre_servicio LIKE :servicio)';
            $params['servicio'] = '%' . $filtros['servicio'] . '%';
        }
        if (($filtros['factura'] ?? '') !== '') {
            $where[] = 'v.factura = :factura';
            $params['factura'] = (int) $filtros['factura'];
        }
        if (!empty($filtros['metodo_pago'])) {
            $where[] = 'v.metodo_pago = :metodo_pago';
            $params['metodo_pago'] = $filtros['metodo_pago'];
        }

        $sql = "SELECT v.*,
                       c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido, c.telefono AS cliente_telefono,
                       u.nombre AS usuario_nombre,
                       (SELECT GROUP_CONCAT(nombre_servicio SEPARATOR ', ') FROM venta_detalle vd2 WHERE vd2.venta_id = v.id) AS servicios
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                INNER JOIN users u ON u.id = v.usuario_id";

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY v.created_at DESC LIMIT ' . self::REPORTE_LIMITE;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
