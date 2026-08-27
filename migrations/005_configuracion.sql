-- Configuración del negocio (fila única, id=1): se usa junto al logo, en el recibo
-- impreso de ventas, y en cualquier otra parte del sistema que necesite estos datos.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 005_configuracion.sql
USE taller;

CREATE TABLE IF NOT EXISTS configuracion (
    id             INT PRIMARY KEY,
    nombre_taller  VARCHAR(150) NOT NULL DEFAULT 'Taller Mecánico Reyes',
    rfc            VARCHAR(13) NULL,
    direccion      VARCHAR(255) NULL,
    telefono       VARCHAR(30) NULL,
    email          VARCHAR(150) NULL,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO configuracion (id, nombre_taller) VALUES (1, 'Taller Mecánico Reyes');
