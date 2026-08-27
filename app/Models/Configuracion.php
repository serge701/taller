<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class Configuracion extends BaseModel
{
    protected string $table    = 'configuracion';
    protected array  $fillable = ['nombre_taller', 'rfc', 'direccion', 'telefono', 'email'];

    private const DEFAULT = [
        'id'            => 1,
        'nombre_taller' => 'Taller Mecánico Reyes',
        'rfc'           => null,
        'direccion'     => null,
        'telefono'      => null,
        'email'         => null,
    ];

    public function obtener(): array
    {
        $row = $this->find(1);
        return $row ?: self::DEFAULT;
    }

    public function actualizar(array $data): void
    {
        $this->update(1, $data);
    }
}
