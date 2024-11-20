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
INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@adminId, 'admintenis', '1144445555', '2000-01-01');
INSERT INTO profesor (id, nombre, email, telefono) VALUES (@adminId, 'admintenis', 'admintenis@test.com', '1144445555');

INSERT INTO profesor (id, nombre, email, telefono) VALUES (@profeId, 'profeuno', 'profe1@test.com', '2215559999');
INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@profeId, 'profeuno', '2215559999', '1999-10-10');

INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@clienteId, 'cliente', '2217774444', '2000-01-20');

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

-- crear cobros
INSERT INTO cobro (monto, fecha, hora, concepto, cliente_id, descripcion)
VALUES (100.0, "2024-11-18", "18:00:00", "clase x", @clienteId, "Una descripcion"),
      (400.0, "2024-11-01", "19:00:00", "clase y", @clienteId, "Otra descripcion");
