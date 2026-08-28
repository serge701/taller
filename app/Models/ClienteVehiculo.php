<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

/**
 * "Memoria" de vehículos por cliente: cada venta/trabajo nuevo llama a registrar()
 * para guardar (o refrescar la fecha de) el vehículo usado, y el formulario de
 * ventas/trabajos consulta porCliente() para prellenar Marca/Modelo/Año/Color la
 * siguiente vez que ese cliente vuelva. No confundir con vehiculo_marcas/vehiculo_modelos
 * (el catálogo general de marcas/modelos que mantiene el admin en /vehiculos).
 */
class ClienteVehiculo extends BaseModel
{
    protected string $table    = 'cliente_vehiculos';
    protected array  $fillable = ['cliente_id', 'marca', 'modelo', 'anio', 'color'];

    /**
     * Vehículos guardados de un cliente, el usado más recientemente primero.
     */
    public function porCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cliente_vehiculos WHERE cliente_id = :id ORDER BY updated_at DESC"
        );
        $stmt->execute(['id' => $clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra el vehículo de una venta/trabajo recién creado: si el cliente ya
     * tiene ese mismo vehículo guardado (comparación insensible a mayúsculas),
     * solo le refresca la fecha (para que aparezca primero la próxima vez);
     * si no, lo agrega como nuevo. Nunca lanza — un fallo aquí no debe tumbar
     * el registro de la venta/trabajo que sí importa.
     */
    public function registrar(int $clienteId, string $marca, ?string $modelo, ?int $anio, ?string $color): void
    {
        $marca  = trim($marca);
        $modelo = $modelo !== null ? trim($modelo) : null;
        $color  = $color !== null ? trim($color) : null;

        if ($marca === '') {
            return;
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM cliente_vehiculos
                 WHERE cliente_id = :cliente_id
                   AND LOWER(marca) = LOWER(:marca)
                   AND LOWER(COALESCE(modelo, '')) = LOWER(COALESCE(:modelo, ''))
                   AND COALESCE(anio, 0) = COALESCE(:anio, 0)
                   AND LOWER(COALESCE(color, '')) = LOWER(COALESCE(:color, ''))
                 LIMIT 1"
            );
            $stmt->execute([
                'cliente_id' => $clienteId,
                'marca'      => $marca,
                'modelo'     => $modelo,
                'anio'       => $anio,
                'color'      => $color,
            ]);
            $existente = $stmt->fetch();

            if ($existente) {
                $this->db->prepare('UPDATE cliente_vehiculos SET updated_at = NOW() WHERE id = :id')
                    ->execute(['id' => $existente['id']]);
                return;
            }

            $this->create([
                'cliente_id' => $clienteId,
                'marca'      => $marca,
                'modelo'     => $modelo,
                'anio'       => $anio,
                'color'      => $color,
            ]);
        } catch (\Throwable $e) {
            // Best-effort: si esto falla no debe afectar el flujo principal de la venta/trabajo.
        }
    }
}
