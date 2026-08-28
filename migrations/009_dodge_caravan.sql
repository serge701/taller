-- Agrega Dodge Caravan / Grand Caravan, minivans muy vendidas en México por décadas
-- que se quedaron fuera de la investigación de la migración 008.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 009_dodge_caravan.sql
USE taller;

INSERT INTO vehiculo_modelos (marca_id, nombre) VALUES
((SELECT id FROM vehiculo_marcas WHERE nombre = 'Dodge'), 'Caravan'),
((SELECT id FROM vehiculo_marcas WHERE nombre = 'Dodge'), 'Grand Caravan');
