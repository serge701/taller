-- Log de consultas VIN: guarda cada búsqueda hecha desde /vin (solo el resumen que
-- se muestra antes de la ficha técnica completa, no los ~150 campos de detalle),
-- para poder revisarlas después en una página aparte.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 016_vin_consultas.sql
USE taller;

CREATE TABLE IF NOT EXISTS vin_consultas (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    vin                VARCHAR(20) NOT NULL,
    usuario_id         INT NOT NULL,
    ok                 TINYINT(1) NOT NULL DEFAULT 1,
    error              VARCHAR(255) NULL,
    model_year         VARCHAR(10) NULL,
    make               VARCHAR(80) NULL,
    model              VARCHAR(80) NULL,
    version            VARCHAR(120) NULL,
    series             VARCHAR(120) NULL,
    body_class         VARCHAR(80) NULL,
    vehicle_type       VARCHAR(80) NULL,
    drive_type         VARCHAR(80) NULL,
    fuel_type_primary  VARCHAR(80) NULL,
    engine_cylinders   VARCHAR(10) NULL,
    displacement_l     VARCHAR(20) NULL,
    doors              VARCHAR(10) NULL,
    created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vin_consultas_usuario FOREIGN KEY (usuario_id) REFERENCES users(id),
    INDEX idx_vin_consultas_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
