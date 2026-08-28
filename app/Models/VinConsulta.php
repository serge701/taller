<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

/**
 * Log de búsquedas hechas en /vin. Guarda solo el resumen que se muestra antes
 * de la ficha técnica completa (año/marca/modelo/versión/carrocería/etc.), no los
 * ~150 campos de detalle que regresa la API de NHTSA.
 */
class VinConsulta extends BaseModel
{
    protected string $table    = 'vin_consultas';
    protected array  $fillable = [
        'vin', 'usuario_id', 'ok', 'error',
        'model_year', 'make', 'model', 'version', 'series',
        'body_class', 'vehicle_type', 'drive_type', 'fuel_type_primary',
        'engine_cylinders', 'displacement_l', 'doors',
    ];

    /**
     * Registra una consulta. Nunca lanza — un fallo aquí no debe afectar la
     * respuesta al usuario que sí le interesa (el resultado del VIN).
     */
    public function registrar(int $usuarioId, string $vin, bool $ok, ?string $error, array $datos = []): void
    {
        try {
            $this->create([
                'vin'               => $vin,
                'usuario_id'        => $usuarioId,
                'ok'                => $ok ? 1 : 0,
                'error'             => $error,
                'model_year'        => $datos['ModelYear'] ?? null,
                'make'              => $datos['Make'] ?? null,
                'model'             => $datos['Model'] ?? null,
                'version'           => $datos['Trim'] ?? null,
                'series'            => $datos['Series'] ?? null,
                'body_class'        => $datos['BodyClass'] ?? null,
                'vehicle_type'      => $datos['VehicleType'] ?? null,
                'drive_type'        => $datos['DriveType'] ?? null,
                'fuel_type_primary' => $datos['FuelTypePrimary'] ?? null,
                'engine_cylinders'  => $datos['EngineCylinders'] ?? null,
                'displacement_l'    => $datos['DisplacementL'] ?? null,
                'doors'             => $datos['Doors'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // Best-effort.
        }
    }

    /**
     * Consultas más recientes primero, con el nombre de quién la hizo, para la
     * página de historial.
     */
    public function recientes(int $limite = 300): array
    {
        $stmt = $this->db->prepare(
            "SELECT vc.*, u.nombre AS usuario_nombre
             FROM vin_consultas vc
             INNER JOIN users u ON u.id = vc.usuario_id
             ORDER BY vc.created_at DESC
             LIMIT :limite"
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
