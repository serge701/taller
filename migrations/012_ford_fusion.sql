-- Agrega Ford Fusion, sedán mediano muy vendido en México (2006-2020) que se
-- quedó fuera de las revisiones anteriores del catálogo.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 012_ford_fusion.sql
USE taller;

INSERT INTO vehiculo_modelos (marca_id, nombre) VALUES
((SELECT id FROM vehiculo_marcas WHERE nombre = 'Ford'), 'Fusion');
