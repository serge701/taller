<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use InvalidArgumentException;

class VehiculoMarca extends BaseModel
{
    protected string $table    = 'vehiculo_marcas';
    protected array  $fillable = ['nombre'];

    /**
     * Catálogo completo marca → modelos, listo para embeber en la vista como JSON
     * y filtrar en el cliente (sin ida y vuelta al servidor al cambiar la marca).
     * Formato: [['id' => 1, 'nombre' => 'Nissan', 'modelos' => [['id' => 1, 'nombre' => 'Versa'], ...]], ...]
     */
    public function todasConModelos(): array
    {
        $stmt = $this->db->query(
            "SELECT vm.id AS marca_id, vm.nombre AS marca_nombre,
                    vmo.id AS modelo_id, vmo.nombre AS modelo_nombre
             FROM vehiculo_marcas vm
             LEFT JOIN vehiculo_modelos vmo ON vmo.marca_id = vm.id
             ORDER BY vm.nombre ASC, vmo.nombre ASC"
        );

        $marcas = [];
        foreach ($stmt->fetchAll() as $row) {
            $marcaId = (int) $row['marca_id'];
            if (!isset($marcas[$marcaId])) {
                $marcas[$marcaId] = [
                    'id'      => $marcaId,
                    'nombre'  => $row['marca_nombre'],
                    'modelos' => [],
                ];
            }
            if ($row['modelo_id'] !== null) {
                $marcas[$marcaId]['modelos'][] = [
                    'id'     => (int) $row['modelo_id'],
                    'nombre' => $row['modelo_nombre'],
                ];
            }
        }

        return array_values($marcas);
    }

    /**
     * Agrega una marca nueva al catálogo. Duplicados (sin importar mayúsculas) se rechazan
     * para no ensuciar los selectores con "Nissan" y "nissan" como opciones distintas.
     */
    public function crearMarca(string $nombre): int
    {
        $nombre = trim($nombre);
        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre de la marca es obligatorio.');
        }

        $stmt = $this->db->prepare("SELECT id FROM vehiculo_marcas WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        if ($stmt->fetch()) {
            throw new InvalidArgumentException('Esa marca ya existe.');
        }

        return $this->create(['nombre' => $nombre]);
    }

    /**
     * Agrega un modelo nuevo a una marca existente. Duplicados dentro de la misma
     * marca (sin importar mayúsculas) se rechazan.
     */
    public function crearModelo(int $marcaId, string $nombre): int
    {
        $nombre = trim($nombre);
        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre del modelo es obligatorio.');
        }
        if (!$this->find($marcaId)) {
            throw new InvalidArgumentException('La marca seleccionada no existe.');
        }

        $stmt = $this->db->prepare(
            "SELECT id FROM vehiculo_modelos WHERE marca_id = :marca_id AND LOWER(nombre) = LOWER(:nombre) LIMIT 1"
        );
        $stmt->execute(['marca_id' => $marcaId, 'nombre' => $nombre]);
        if ($stmt->fetch()) {
            throw new InvalidArgumentException('Ese modelo ya existe para esa marca.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO vehiculo_modelos (marca_id, nombre) VALUES (:marca_id, :nombre)"
        );
        $stmt->execute(['marca_id' => $marcaId, 'nombre' => $nombre]);
        return (int) $this->db->lastInsertId();
    }
}
