<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class Cliente extends BaseModel
{
    protected string $table    = 'clientes';
    protected array  $fillable = [
        'nombre', 'apellido_paterno', 'telefono', 'email', 'direccion',
        'rfc', 'razon_social', 'regimen_fiscal', 'cp_fiscal',
    ];

    public function todosConVentas(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*,
                    COUNT(v.id) AS total_ventas
             FROM clientes c
             LEFT JOIN ventas v ON v.cliente_id = c.id
             GROUP BY c.id
             ORDER BY c.apellido_paterno ASC, c.nombre ASC"
        );
        return $stmt->fetchAll();
    }

    public function buscar(string $q): array
    {
        $like = "%{$q}%";
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE nombre LIKE ? OR apellido_paterno LIKE ? OR telefono LIKE ? OR email LIKE ?
             ORDER BY apellido_paterno ASC, nombre ASC LIMIT 20"
        );
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Vehículos distintos que este cliente ha traído, derivados de sus ventas y
     * trabajos (aún no hay una tabla propia de vehículos por cliente). Se agrupan
     * por marca/modelo/año/color y se ordenan por la visita más reciente primero.
     */
    public function vehiculosHistoricos(int $clienteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color,
                    MAX(fecha) AS ultima_vez
             FROM (
                 SELECT vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color, created_at AS fecha
                 FROM ventas WHERE cliente_id = :id1
                 UNION ALL
                 SELECT vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color, created_at AS fecha
                 FROM trabajos WHERE cliente_id = :id2
             ) t
             WHERE vehiculo_marca IS NOT NULL AND vehiculo_marca <> ''
             GROUP BY vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color
             ORDER BY ultima_vez DESC"
        );
        $stmt->execute(['id1' => $clienteId, 'id2' => $clienteId]);
        return $stmt->fetchAll();
    }
}
