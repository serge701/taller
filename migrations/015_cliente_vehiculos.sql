-- Vehículos ligados al cliente: cada venta/trabajo nuevo registra (o actualiza la
-- fecha de) el vehículo usado, para poder prellenar Marca/Modelo/Año/Color la
-- próxima vez que ese cliente vuelva. No sustituye las columnas vehiculo_* de
-- ventas/trabajos (esas siguen siendo el snapshot histórico de cada transacción);
-- esta tabla es la "memoria" por cliente que alimenta el prellenado.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 015_cliente_vehiculos.sql
USE taller;

CREATE TABLE IF NOT EXISTS cliente_vehiculos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    marca      VARCHAR(80) NOT NULL,
    modelo     VARCHAR(80) NULL,
    anio       SMALLINT NULL,
    color      VARCHAR(40) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cliente_vehiculos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_cliente_vehiculos_cliente (cliente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill: reconstruye la "memoria" a partir del histórico ya existente en
-- ventas y trabajos, agrupando por vehículo exacto y tomando la fecha más
-- reciente en que se usó como updated_at (para que el prellenado arranque
-- mostrando primero el vehículo más reciente de cada cliente).
INSERT INTO cliente_vehiculos (cliente_id, marca, modelo, anio, color, created_at, updated_at)
SELECT cliente_id, vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color,
       MIN(fecha), MAX(fecha)
FROM (
    SELECT cliente_id, vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color, created_at AS fecha
    FROM ventas
    WHERE vehiculo_marca IS NOT NULL AND vehiculo_marca <> ''
    UNION ALL
    SELECT cliente_id, vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color, created_at AS fecha
    FROM trabajos
    WHERE vehiculo_marca IS NOT NULL AND vehiculo_marca <> ''
) t
GROUP BY cliente_id, vehiculo_marca, vehiculo_modelo, vehiculo_anio, vehiculo_color;
