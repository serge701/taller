-- Notas de pago: refaccionarias que surten partes a crédito y pasan a cobrar después.
-- Cada nota nace "Pendiente" y se marca "Pagado" cuando se liquida, con comentarios opcionales.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 018_notas_pago.sql
USE taller;

CREATE TABLE IF NOT EXISTS notas_pago (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    monto       DECIMAL(10,2) NOT NULL,
    descripcion TEXT NOT NULL,
    trabajo_id  INT NULL,
    usuario_id  INT NOT NULL,
    estado      ENUM('Pendiente','Pagado') NOT NULL DEFAULT 'Pendiente',
    comentarios TEXT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pagado_at   DATETIME NULL,
    CONSTRAINT fk_notas_pago_trabajo FOREIGN KEY (trabajo_id) REFERENCES trabajos(id) ON DELETE SET NULL,
    CONSTRAINT fk_notas_pago_usuario FOREIGN KEY (usuario_id) REFERENCES users(id),
    INDEX idx_notas_pago_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
