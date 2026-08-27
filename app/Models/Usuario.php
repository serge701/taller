<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class Usuario extends BaseModel
{
    protected string $table    = 'users';
    protected array  $fillable = [
        'usuario', 'password', 'nombre', 'nivel', 'activo', 'foto',
    ];

    public function findByUsuario(string $usuario): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE usuario = :usuario LIMIT 1"
        );
        $stmt->execute(['usuario' => $usuario]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function touchLastLogin(int $id): void
    {
        $this->db->prepare(
            "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id"
        )->execute(['id' => $id]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $this->db->prepare(
            "UPDATE {$this->table} SET password = :hash WHERE id = :id"
        )->execute(['hash' => $hash, 'id' => $id]);
    }
}
