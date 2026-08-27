-- Separa "Tarjeta" en Débito/Crédito
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 003_metodo_pago_tarjetas.sql
USE taller;

-- Por si quedara algún registro con el valor genérico anterior.
UPDATE ventas SET metodo_pago = 'Tarjeta de Crédito' WHERE metodo_pago = 'Tarjeta';

ALTER TABLE ventas
    MODIFY COLUMN metodo_pago ENUM('Efectivo','Tarjeta de Débito','Tarjeta de Crédito','Transferencia')
    NOT NULL DEFAULT 'Efectivo';
