<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use InvalidArgumentException;

class Trabajo extends BaseModel
{
    protected string $table    = 'trabajos';
    protected array  $fillable = [
        'cliente_id', 'usuario_id', 'vehiculo_marca', 'vehiculo_color', 'vehiculo_placas',
        'anticipo', 'estado', 'venta_id', 'cerrado_at',
    ];

    public function todosConDetalle(): array
    {
        $stmt = $this->db->query(
            "SELECT t.*,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    u.nombre AS usuario_nombre,
                    (SELECT COUNT(*) FROM trabajo_servicios ts WHERE ts.trabajo_id = t.id) AS total_servicios,
                    (SELECT COUNT(*) FROM trabajo_comentarios tc WHERE tc.trabajo_id = t.id) AS total_comentarios
             FROM trabajos t
             INNER JOIN clientes c ON c.id = t.cliente_id
             INNER JOIN users u ON u.id = t.usuario_id
             ORDER BY (t.estado = 'Abierto') DESC, t.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function findConDetalle(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    c.telefono AS cliente_telefono,
                    u.nombre AS usuario_nombre
             FROM trabajos t
             INNER JOIN clientes c ON c.id = t.cliente_id
             INNER JOIN users u ON u.id = t.usuario_id
             WHERE t.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function servicios(int $trabajoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM trabajo_servicios WHERE trabajo_id = :id ORDER BY id ASC"
        );
        $stmt->execute(['id' => $trabajoId]);
        return $stmt->fetchAll();
    }

    public function comentarios(int $trabajoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT tc.*, u.nombre AS usuario_nombre
             FROM trabajo_comentarios tc
             INNER JOIN users u ON u.id = tc.usuario_id
             WHERE tc.trabajo_id = :id
             ORDER BY tc.created_at DESC"
        );
        $stmt->execute(['id' => $trabajoId]);
        return $stmt->fetchAll();
    }

    /**
     * Crea un trabajo junto con la lista de servicios planeados.
     * $servicios = [['servicio_id' => int, 'nombre' => string], ...]
     */
    public function crearConServicios(int $clienteId, int $usuarioId, array $vehiculo, float $anticipo, array $servicios): int
    {
        if (trim((string) ($vehiculo['marca'] ?? '')) === '' || trim((string) ($vehiculo['color'] ?? '')) === '') {
            throw new InvalidArgumentException('Marca y color del vehículo son obligatorios.');
        }
        if ($servicios === []) {
            throw new InvalidArgumentException('Agrega al menos un servicio planeado.');
        }

        $this->db->beginTransaction();
        try {
            $trabajoId = $this->create([
                'cliente_id'      => $clienteId,
                'usuario_id'      => $usuarioId,
                'vehiculo_marca'  => trim((string) $vehiculo['marca']),
                'vehiculo_color'  => trim((string) $vehiculo['color']),
                'vehiculo_placas' => trim((string) ($vehiculo['placas'] ?? '')) !== '' ? trim((string) $vehiculo['placas']) : null,
                'anticipo'        => max(0, round($anticipo, 2)),
                'estado'          => 'Abierto',
            ]);

            $stmt = $this->db->prepare(
                "INSERT INTO trabajo_servicios (trabajo_id, servicio_id, nombre_servicio)
                 VALUES (:trabajo_id, :servicio_id, :nombre_servicio)"
            );
            foreach ($servicios as $s) {
                $nombre = trim((string) ($s['nombre'] ?? ''));
                if ($nombre === '') {
                    continue;
                }
                $stmt->execute([
                    'trabajo_id'      => $trabajoId,
                    'servicio_id'     => !empty($s['servicio_id']) ? (int) $s['servicio_id'] : null,
                    'nombre_servicio' => $nombre,
                ]);
            }

            $this->db->commit();
            return $trabajoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function agregarComentario(int $trabajoId, int $usuarioId, string $texto): void
    {
        $this->db->prepare(
            "INSERT INTO trabajo_comentarios (trabajo_id, usuario_id, comentario) VALUES (:trabajo_id, :usuario_id, :comentario)"
        )->execute([
            'trabajo_id' => $trabajoId,
            'usuario_id' => $usuarioId,
            'comentario' => $texto,
        ]);
    }

    public function cerrar(int $trabajoId, int $ventaId): void
    {
        $this->db->prepare(
            "UPDATE trabajos SET estado = 'Cerrado', venta_id = :venta_id, cerrado_at = NOW() WHERE id = :id"
        )->execute(['venta_id' => $ventaId, 'id' => $trabajoId]);
    }

    /**
     * Resumen de trabajos abiertos para el dashboard: cuántos hay, cuánto suman
     * de anticipo, y cuántos llevan abiertos más de una semana (posible foco de atención).
     */
    public function estadisticasAbiertos(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(anticipo), 0) AS anticipos,
                    SUM(CASE WHEN created_at <= NOW() - INTERVAL 7 DAY THEN 1 ELSE 0 END) AS atrasados
             FROM trabajos WHERE estado = 'Abierto'"
        );
        return $stmt->fetch() ?: ['total' => 0, 'anticipos' => 0, 'atrasados' => 0];
    }

    /**
     * Trabajos abiertos, del que lleva más tiempo esperando al más reciente,
     * para destacarlos en el dashboard.
     */
    public function abiertosOrdenados(int $limite = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*,
                    c.nombre AS cliente_nombre, c.apellido_paterno AS cliente_apellido,
                    DATEDIFF(NOW(), t.created_at) AS dias_abierto,
                    (SELECT COUNT(*) FROM trabajo_servicios ts WHERE ts.trabajo_id = t.id) AS total_servicios
             FROM trabajos t
             INNER JOIN clientes c ON c.id = t.cliente_id
             WHERE t.estado = 'Abierto'
             ORDER BY t.created_at ASC
             LIMIT :limite"
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
