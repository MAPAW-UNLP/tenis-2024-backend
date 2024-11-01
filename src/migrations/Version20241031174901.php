<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031174901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clases ADD cliente_id INT DEFAULT NULL, ADD profesor_id INT DEFAULT NULL, ADD cancha_id INT DEFAULT NULL, ADD fecha DATE DEFAULT NULL, ADD hora_ini TIME DEFAULT NULL, ADD hora_fin TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE clases ADD CONSTRAINT FK_67CBBF10DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE clases ADD CONSTRAINT FK_67CBBF10E52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
        $this->addSql('CREATE INDEX IDX_67CBBF10DE734E51 ON clases (cliente_id)');
        $this->addSql('CREATE INDEX IDX_67CBBF10E52BD977 ON clases (profesor_id)');

        // Crear una cancha
        $this->addSql('INSERT INTO cancha (tipo, nombre) values ("pasto", "Cancha 1")');
        $this->addSql("SET @canchaId = (SELECT id FROM cancha WHERE nombre = 'Cancha 1')");

        // Asignar clases existentes a cliente y profesor
        $this->addSql("SET @clienteId = (SELECT id FROM usuario WHERE username = 'cliente')");
        $this->addSql("SET @profeId = (SELECT id FROM usuario WHERE username = 'profe')");

        $this->addSql('UPDATE clases SET cliente_id = @clienteId, profesor_id = @profeId, cancha_id = @canchaId, fecha = \'2024-11-01\', hora_ini = \'09:00:00\', hora_fin = \'10:00:00\' WHERE tipo = \'INDIVIDUAL\' AND importe = 100');
        $this->addSql('UPDATE clases SET cliente_id = @clienteId, profesor_id = @profeId, cancha_id = @canchaId, fecha = \'2024-11-01\', hora_ini = \'10:00:00\', hora_fin = \'11:00:00\' WHERE tipo = \'GRUPAL\' AND importe = 50');

    }

    public function down(Schema $schema): void
    {
        // Revertir actualizaciones de clases
        $this->addSql('UPDATE clases SET cliente_id = NULL, profesor_id = NULL, cancha_id = NULL, fecha = NULL, hora_ini = NULL, hora_fin = NULL WHERE tipo = "INDIVIDUAL" AND importe = 100');
        $this->addSql('UPDATE clases SET cliente_id = NULL, profesor_id = NULL, cancha_id = NULL, fecha = NULL, hora_ini = NULL, hora_fin = NULL WHERE tipo = "GRUPAL" AND importe = 50');

        // Eliminar la cancha creada
        $this->addSql('DELETE FROM cancha WHERE nombre = "Cancha 1"');

        $this->addSql('ALTER TABLE clases DROP FOREIGN KEY FK_67CBBF10DE734E51');
        $this->addSql('ALTER TABLE clases DROP FOREIGN KEY FK_67CBBF10E52BD977');
        $this->addSql('DROP INDEX IDX_67CBBF10DE734E51 ON clases');
        $this->addSql('DROP INDEX IDX_67CBBF10E52BD977 ON clases');
        $this->addSql('ALTER TABLE clases DROP cliente_id, DROP profesor_id, DROP cancha_id, DROP fecha, DROP hora_ini, DROP hora_fin');
    }
}
