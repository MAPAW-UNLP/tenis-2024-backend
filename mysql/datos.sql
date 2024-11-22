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

-- crear clases
INSERT INTO clases (tipo, importe, cliente_id, profesor_id, cancha_id, fecha, hora_ini, hora_fin)
VALUES('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-07', '08:00:00', '09:00:00'),
      ('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-07', '13:00:00', '14:00:00'),
      ('GRUPAL', 100, @clienteId, @profeId, @canchaId, '2024-11-08', '15:00:00', '16:00:00'),
      ('GRUPAL', 100, @clienteId, @profeId, @canchaId, '2024-11-09', '19:00:00', '20:00:00'),
      ('GRUPAL', 100, @clienteId, @profeId, @canchaId, '2024-11-11', '09:00:00', '10:00:00'),
      ('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-13', '09:00:00', '10:00:00'),
      ('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-14', '09:00:00', '10:00:00'),
      ('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-15', '19:00:00', '20:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-16', '10:00:00', '11:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-17', '10:00:00', '11:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-18', '10:00:00', '11:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-19', '10:00:00', '11:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-20', '10:00:00', '11:00:00'),
      ('INDIVIDUAL', 100, @clienteId, @profeId, @canchaId, '2024-11-15', '19:00:00', '20:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-21', '15:00:00', '16:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-22', '16:00:00', '17:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-23', '17:00:00', '18:00:00'),
      ('GRUPAL', 50, @clienteId, @profeId, @canchaId, '2024-11-24', '18:00:00', '19:00:00');


-- Datos de prueba para entidades faltantes
INSERT INTO constancia_mantenimiento (fecha) VALUES ('2024-11-01');
INSERT INTO cuenta (cliente_id, importe, fecha) VALUES (@clienteId, 1000, '2024-11-01');
INSERT INTO estado (descripcion) VALUES ('Pendiente');
INSERT INTO grupo (reserva_id, cliente_id) VALUES (1, @clienteId);
INSERT INTO horario_disponible (fecha, hora_ini, hora_fin, profesor_id) VALUES ('2024-11-01', '08:00:00', '10:00:00', @profeId);
INSERT INTO item_alquiler (description, importe) VALUES ('Raqueta', 50);
INSERT INTO periodo_ausencia (fecha_ini, fecha_fin, motivo, profesor_id, estado_id) VALUES ('2024-11-01', '2024-11-15', 'Vacaciones', @profeId, 1);
--INSERT INTO persona (nombre, telefono, fechanac, escliente, visible) VALUES ('John Doe', '123456789', '1990-01-01', true, true);
INSERT INTO proveedor (nombre, telefono) VALUES ('Proveedor 1', '123456789');
INSERT INTO replicas (id_reserva, ultimo_mes) VALUES (1, 11);
INSERT INTO reserva (cancha_id, fecha, hora_ini, hora_fin, profesor_id, replica, estado_id) VALUES (@canchaId, '2024-11-01', '08:00:00', '09:00:00', @profeId, 0, 1);
INSERT INTO suspension_clase (fecha, hora, profesor_id, estado_id, reserva_id, motivo) VALUES ('2024-11-01', '08:00:00', @profeId, 1, 1, 'Motivo de prueba');
