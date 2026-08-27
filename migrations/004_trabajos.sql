-- Trabajos: reparaciones largas (ej. motores) que se registran aparte de las ventas
-- y que al entregarse se cierran generando una Venta normal.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 004_trabajos.sql
USE taller;

CREATE TABLE IF NOT EXISTS trabajos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id       INT NOT NULL,
    usuario_id       INT NOT NULL,
    vehiculo_marca   VARCHAR(80) NOT NULL,
    vehiculo_color   VARCHAR(40) NOT NULL,
    vehiculo_placas  VARCHAR(20) NULL,
    anticipo         DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado           ENUM('Abierto','Cerrado') NOT NULL DEFAULT 'Abierto',
    venta_id         INT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cerrado_at       DATETIME NULL,
    CONSTRAINT fk_trabajos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_trabajos_usuario FOREIGN KEY (usuario_id) REFERENCES users(id),
    CONSTRAINT fk_trabajos_venta   FOREIGN KEY (venta_id) REFERENCES ventas(id),
    INDEX idx_trabajos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Servicios planeados/pendientes del trabajo (sin precio: eso se define hasta cerrarlo).
CREATE TABLE IF NOT EXISTS trabajo_servicios (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    trabajo_id       INT NOT NULL,
    servicio_id      INT NULL,
    nombre_servicio  VARCHAR(150) NOT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ts_trabajo  FOREIGN KEY (trabajo_id) REFERENCES trabajos(id) ON DELETE CASCADE,
    CONSTRAINT fk_ts_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Línea de tiempo de avance del trabajo.
CREATE TABLE IF NOT EXISTS trabajo_comentarios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    trabajo_id   INT NOT NULL,
    usuario_id   INT NOT NULL,
    comentario   TEXT NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tc_trabajo FOREIGN KEY (trabajo_id) REFERENCES trabajos(id) ON DELETE CASCADE,
    CONSTRAINT fk_tc_usuario FOREIGN KEY (usuario_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
