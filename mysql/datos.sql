-- crear usuarios
INSERT INTO usuario (username, password, rol_por_defecto ) VALUES ("admin", "admin", "ROLE_ADMIN");
INSERT INTO usuario (username, password, rol_por_defecto) VALUES ('profe', 'profe', 'ROLE_PROFESOR');
INSERT INTO usuario (username, password, rol_por_defecto) VALUES ('cliente', 'cliente', 'ROLE_CLIENTE');

-- crear cancha
INSERT INTO cancha (tipo, nombre) values ("Hierba", "Cancha 1");

-- obtener ids
SET @adminId = (SELECT id FROM usuario WHERE username = 'admin');
SET @canchaId = (SELECT id FROM cancha WHERE nombre = 'Cancha 1');
SET @clienteId = (SELECT id FROM usuario WHERE username = 'cliente');
SET @profeId = (SELECT id FROM usuario WHERE username = 'profe');

-- crear roles
INSERT INTO administrador (id) VALUES (@adminId);
INSERT INTO cliente (id, nombre, telefono, fecha_nac, es_alumno, visible) VALUES (@adminId, 'admintenis', '1144445555', '2000-01-01', true, true);
INSERT INTO profesor (id, nombre, email, telefono) VALUES (@adminId, 'admintenis', 'admintenis@test.com', '1144445555');

INSERT INTO profesor (id, nombre, email, telefono) VALUES (@profeId, 'profeuno', 'profe1@test.com', '2215559999');
INSERT INTO cliente (id, nombre, telefono, fecha_nac, es_alumno, visible) VALUES (@profeId, 'profeuno', '2215559999', '1999-10-10', true, true);

INSERT INTO cliente (id, nombre, telefono, fecha_nac, es_alumno, visible) VALUES (@clienteId, 'cliente', '2217774444', '2000-01-20', true, true);


-- tipos de clase
INSERT INTO clases (tipo, importe) VALUES ('INDIVIDUAL', 15000);
INSERT INTO clases (tipo, importe) VALUES ('GRUPAL', 10000);


-- estados 
INSERT INTO estado(descripcion)
VALUES("ASIGNADO"),("CANCELADO")("CONSUMIDO");--(1/2/3)


-- Datos de prueba para entidades faltantes
INSERT INTO constancia_mantenimiento (fecha) VALUES ('2024-11-01');
INSERT INTO cuenta (cliente_id, importe, fecha) VALUES (@clienteId, 1000, '2024-11-01');
INSERT INTO grupo (reserva_id, cliente_id) VALUES (1, @clienteId);
INSERT INTO horario_disponible (fecha, hora_ini, hora_fin, profesor_id) VALUES ('2024-11-01', '08:00:00', '10:00:00', @profeId);
INSERT INTO item_alquiler (description, importe) VALUES ('Raqueta', 50);
INSERT INTO periodo_ausencia (fecha_ini, fecha_fin, motivo, profesor_id, estado_id) VALUES ('2024-11-01', '2024-11-15', 'Vacaciones', @profeId, 1);
INSERT INTO proveedor (nombre, telefono) VALUES ('Proveedor 1', '123456789');
INSERT INTO replicas (id_reserva, ultimo_mes) VALUES (1, 11);
INSERT INTO suspension_clase (fecha, hora, profesor_id, estado_id, reserva_id, motivo) VALUES ('2024-11-01', '08:00:00', @profeId, 1, 1, 'Motivo de prueba');


-- crear cobros
INSERT INTO cobro (monto, fecha, hora, concepto, cliente_id, descripcion, id_tipo_clase)
VALUES (100.0, "2024-11-18", "18:00:00", "clase a", @clienteId, "Una descripcion", 1),
       (1000.0, "2024-11-19", "10:00:00", "clase b", @clienteId, "Una descripcion", 1),
       (2000.0, "2024-11-20", "18:00:00", "clase c", @clienteId, "Una descripcion", 1),
       (3000.0, "2024-11-21", "10:00:00", "clase d", @clienteId, "Una descripcion", 1),
       (500.0, "2024-11-22", "18:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (600.0, "2024-11-23", "18:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (700.0, "2024-11-24", "18:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (800.0, "2024-11-25", "10:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (900.0, "2024-11-18", "18:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (1000.0, "2024-11-17", "18:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (1000.0, "2024-11-16", "10:00:00", "pago x", @clienteId, "Una descripcion", 2),
       (1000.0, "2024-11-26", "18:00:00", "pago x", @clienteId, "Una descripcion", 1),
       (400.0, "2024-11-27", "19:00:00", "clase x", @clienteId, "Una descripcion", 1),
       (100.0, "2024-10-27", "18:00:00", "clase x", @clienteId, "Una descripcion", 1),
       (100.0, "2024-11-28", "18:00:00", "clase x", @clienteId, "Una descripcion", 1),
      (400.0, "2024-11-01", "19:00:00", "clase y", @clienteId, "Otra descripcion", 1);


-- crear clases(reserva)
INSERT INTO reserva(cancha_id, fecha, hora_ini, hora_fin, profesor_id, replica, estado_id, id_tipo_clase, pago_id)
VALUES (@canchaId, "2024-12-16", "15:00:00", "16:00:00", @profeId, 0, 1, 1, null);

SET @reservaId = (SELECT id FROM reserva LIMIT 1);
 
-- asignar reserva(clase) a cliente (cambiar a cliente_id)
INSERT INTO grupo(reserva_id, cliente_id)
VALUES(@reservaId, @clienteId);