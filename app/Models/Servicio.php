<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class Servicio extends BaseModel
{
    protected string $table    = 'servicios';
    protected array  $fillable = [
        'nombre', 'precio', 'activo',
    ];

    public function activos(): array
    {
        return $this->all(['activo' => 1], 'nombre ASC', 1000);
    }

    public function buscar(string $q): array
    {
        $like = "%{$q}%";
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE activo = 1 AND nombre LIKE ? ORDER BY nombre ASC LIMIT 20"
        );
        $stmt->execute([$like]);
        return $stmt->fetchAll();
    }
}
