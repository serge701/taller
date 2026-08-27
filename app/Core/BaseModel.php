<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use InvalidArgumentException;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected array $fillable = [];
    protected bool $softDelete = false;

    public function __construct()
    {
        $this->db = Database::conn();
    }

    public function getConnection(): PDO
    {
        return $this->db;
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        if ($this->softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function all(array $where = [], string $orderBy = 'id DESC', int $limit = 500): array
    {
        $sql    = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($this->softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }

        foreach ($where as $col => $val) {
            $this->assertColumn($col);
            $sql .= " AND {$col} = :w_{$col}";
            $params["w_{$col}"] = $val;
        }

        $sql .= ' ORDER BY ' . $this->safeOrderBy($orderBy);
        $sql .= ' LIMIT ' . (int) $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $data = $this->onlyFillable($data);

        $cols         = array_keys($data);
        $placeholders = array_map(static fn($c) => ":{$c}", $cols);

        $sql = sprintf(
            "INSERT INTO {$this->table} (%s) VALUES (%s)",
            implode(', ', $cols),
            implode(', ', $placeholders)
        );

        $this->db->prepare($sql)->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->onlyFillable($data);
        if ($data === []) {
            return false;
        }

        $sets   = array_map(static fn($c) => "{$c} = :{$c}", array_keys($data));
        $sql    = sprintf("UPDATE {$this->table} SET %s WHERE id = :id", implode(', ', $sets));
        $params = $data + ['id' => $id];

        return $this->db->prepare($sql)->execute($params);
    }

    public function delete(int $id): bool
    {
        if ($this->softDelete) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id");
        } else {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        }
        return $stmt->execute(['id' => $id]);
    }

    protected function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function assertColumn(string $col): void
    {
        $allowed = array_merge($this->fillable, ['id', 'created_at']);
        if (!in_array($col, $allowed, true)) {
            throw new InvalidArgumentException("Columna no permitida: {$col}");
        }
    }

    protected function safeOrderBy(string $orderBy): string
    {
        if (preg_match('/^([a-zA-Z_]+)\s*(ASC|DESC)?$/i', trim($orderBy), $m)) {
            $col     = $m[1];
            $dir     = strtoupper($m[2] ?? 'ASC');
            $allowed = array_merge($this->fillable, ['id', 'created_at', 'updated_at']);
            if (in_array($col, $allowed, true)) {
                return "{$col} {$dir}";
            }
        }
        return 'id DESC';
    }
}
