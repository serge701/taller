<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class NotaPago extends BaseModel
{
    protected string $table    = 'notas_pago';
    protected array  $fillable = [
        'monto', 'descripcion', 'trabajo_id', 'usuario_id', 'estado', 'comentarios', 'pagado_at',
    ];

    public function todasConDetalle(): array
    {
        $stmt = $this->db->query(
            "SELECT n.*,
                    u.nombre AS usuario_nombre,
                    c.nombre AS trabajo_cliente_nombre, c.apellido_paterno AS trabajo_cliente_apellido,
                    t.vehiculo_marca, t.vehiculo_modelo
             FROM notas_pago n
             INNER JOIN users u ON u.id = n.usuario_id
             LEFT JOIN trabajos t ON t.id = n.trabajo_id
             LEFT JOIN clientes c ON c.id = t.cliente_id
             ORDER BY (n.estado = 'Pendiente') DESC, n.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function marcarPagada(int $id, ?string $comentarios): bool
    {
        return $this->db->prepare(
            "UPDATE notas_pago SET estado = 'Pagado', pagado_at = NOW(), comentarios = :comentarios WHERE id = :id"
        )->execute(['comentarios' => $comentarios, 'id' => $id]);
    }

    public function totalPendiente(): float
    {
        $stmt = $this->db->query("SELECT COALESCE(SUM(monto), 0) FROM notas_pago WHERE estado = 'Pendiente'");
        return (float) $stmt->fetchColumn();
    }

    /**
     * Total y conteo de notas pendientes de pago, para el KPI del dashboard.
     */
    public function estadisticasPendientes(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total, COALESCE(SUM(monto), 0) AS monto
             FROM notas_pago WHERE estado = 'Pendiente'"
        );
        return $stmt->fetch() ?: ['total' => 0, 'monto' => 0];
    }

    /**
     * Notas pendientes, de la más antigua a la más reciente, para la sección
     * "Notas de Pago" del dashboard.
     */
    public function pendientesOrdenadas(int $limite = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT n.*,
                    c.nombre AS trabajo_cliente_nombre, c.apellido_paterno AS trabajo_cliente_apellido
             FROM notas_pago n
             LEFT JOIN trabajos t ON t.id = n.trabajo_id
             LEFT JOIN clientes c ON c.id = t.cliente_id
             WHERE n.estado = 'Pendiente'
             ORDER BY n.created_at ASC
             LIMIT :limite"
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
