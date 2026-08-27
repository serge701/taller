-- Taller Mecánico Reyes — esquema inicial
--
-- IMPORTANTE (Windows/XAMPP): importa este archivo forzando utf8mb4 en el cliente,
-- si no, los acentos de los INSERT de ejemplo se corrompen:
--   mysql --default-character-set=utf8mb4 -u root < 001_init.sql
CREATE DATABASE IF NOT EXISTS taller CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taller;

-- ─────────────────────────────────────────────────────────
-- Usuarios del sistema
-- ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario     VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    nombre      VARCHAR(150) NOT NULL,
    nivel       ENUM('Admin','Usuario') NOT NULL DEFAULT 'Usuario',
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    last_login  DATETIME NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────
-- Clientes (+ datos fiscales opcionales para facturación)
-- ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS clientes (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(100) NOT NULL,
    apellido_paterno  VARCHAR(100) NOT NULL,
    telefono          VARCHAR(30) NOT NULL,
    email             VARCHAR(150) NULL,
    direccion         VARCHAR(255) NULL,
    rfc               VARCHAR(13) NULL,
    razon_social      VARCHAR(200) NULL,
    regimen_fiscal    VARCHAR(150) NULL,
    cp_fiscal         VARCHAR(10) NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clientes_nombre (apellido_paterno, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────
-- Servicios (catálogo)
-- ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS servicios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    precio      DECIMAL(10,2) NOT NULL DEFAULT 0,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────
-- Ventas (encabezado) — incluye vehículo atendido y facturación
-- ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ventas (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id       INT NOT NULL,
    usuario_id       INT NOT NULL,
    metodo_pago      ENUM('Efectivo','Tarjeta','Transferencia') NOT NULL DEFAULT 'Efectivo',
    factura          TINYINT(1) NOT NULL DEFAULT 0,
    subtotal         DECIMAL(10,2) NOT NULL DEFAULT 0,
    iva              DECIMAL(10,2) NOT NULL DEFAULT 0,
    total            DECIMAL(10,2) NOT NULL DEFAULT 0,
    vehiculo_marca   VARCHAR(80) NOT NULL,
    vehiculo_color   VARCHAR(40) NOT NULL,
    vehiculo_placas  VARCHAR(20) NULL,
    comentarios      TEXT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ventas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_ventas_usuario FOREIGN KEY (usuario_id) REFERENCES users(id),
    INDEX idx_ventas_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────
-- Detalle de venta (líneas de servicios, con snapshot de precio/nombre)
-- ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS venta_detalle (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    venta_id         INT NOT NULL,
    servicio_id      INT NULL,
    nombre_servicio  VARCHAR(150) NOT NULL,
    precio           DECIMAL(10,2) NOT NULL,
    cantidad         INT NOT NULL DEFAULT 1,
    subtotal         DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_venta FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────
-- Usuario administrador inicial (usuario: admin / contraseña: admin123)
-- Cambia la contraseña después de tu primer login.
-- ─────────────────────────────────────────────────────────
INSERT INTO users (usuario, password, nombre, nivel, activo) VALUES
('admin', '$2y$10$eLxim.dKWbpi3fpI6MAZcuymnbRTzpw4ZnLZ0r6LrRY6gYmWIezY6', 'Administrador', 'Admin', 1);

-- ─────────────────────────────────────────────────────────
-- Servicios de ejemplo (puedes editarlos/eliminarlos desde el sistema)
-- ─────────────────────────────────────────────────────────
INSERT INTO servicios (nombre, precio, activo) VALUES
('Cambio de aceite y filtro', 450.00, 1),
('Afinación mayor', 1800.00, 1),
('Cambio de balatas (par)', 850.00, 1),
('Alineación y balanceo', 500.00, 1),
('Revisión de frenos', 300.00, 1),
('Cambio de batería', 1600.00, 1),
('Diagnóstico computarizado', 350.00, 1);
