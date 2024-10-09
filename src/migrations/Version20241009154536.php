<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241009154536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Obtener usuario admin
        $this->addSql("SET @adminId = (SELECT id FROM usuario WHERE username = 'admin')");

        // Crear entidad Cliente para admin
        $this->addSql("INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@adminId, 'admintenis', '1144445555', '2000-01-01')");

        // Crear entidad Profesor para admin
        $this->addSql("INSERT INTO profesor (id, nombre, email, telefono) VALUES (@adminId, 'admintenis', 'admintenis@test.com', '1144445555')");

        // Crear usuario profe
        $this->addSql("INSERT INTO usuario (username, password, rol_por_defecto) VALUES ('profe', 'profe', 'ROLE_PROFESOR')");
        $this->addSql("SET @profeId = (SELECT id FROM usuario WHERE username = 'profe')");

        // Crear entidad Profesor para profe
        $this->addSql("INSERT INTO profesor (id, nombre, email, telefono) VALUES (@profeId, 'profeuno', 'profe1@test.com', '2215559999')");

        // Crear entidad Cliente para profe
        $this->addSql("INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@profeId, 'profeuno', '2215559999', '1999-10-10')");

        // Crear usuario cliente
        $this->addSql("INSERT INTO usuario (username, password, rol_por_defecto) VALUES ('cliente', 'cliente', 'ROLE_CLIENTE')");
        $this->addSql("SET @clienteId = (SELECT id FROM usuario WHERE username = 'cliente')");

        // Crear entidad Cliente para cliente
        $this->addSql("INSERT INTO cliente (id, nombre, telefono, fecha_nac) VALUES (@clienteId, 'cliente', '2217774444', '2000-01-20')");
    }

    public function down(Schema $schema): void
    {
        // Eliminar entidad Cliente para admin
        $this->addSql("DELETE FROM cliente WHERE id = (SELECT id FROM usuario WHERE username = 'admin')");
    
        // Eliminar entidad Profesor para admin
        $this->addSql("DELETE FROM profesor WHERE id = (SELECT id FROM usuario WHERE username = 'admin')");
    
        // Eliminar entidad Cliente para profe
        $this->addSql("DELETE FROM cliente WHERE id = (SELECT id FROM usuario WHERE username = 'profe')");
    
        // Eliminar entidad Profesor para profe
        $this->addSql("DELETE FROM profesor WHERE id = (SELECT id FROM usuario WHERE username = 'profe')");
    
        // Eliminar usuario profe
        $this->addSql("DELETE FROM usuario WHERE username = 'profe'");
    
        // Eliminar entidad Cliente para cliente
        $this->addSql("DELETE FROM cliente WHERE id = (SELECT id FROM usuario WHERE username = 'cliente')");
    
        // Eliminar usuario cliente
        $this->addSql("DELETE FROM usuario WHERE username = 'cliente'");
    }
}
