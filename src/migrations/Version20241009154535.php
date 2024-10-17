<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241009154535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cobro DROP FOREIGN KEY FK_F0A26526FC28E5EE');
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, telefono VARCHAR(15) NOT NULL, fecha_nac DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25BF396750 FOREIGN KEY (id) REFERENCES usuario (id)');
        $this->addSql('DROP TABLE alumno');
        $this->addSql('DROP INDEX IDX_F0A26526FC28E5EE ON cobro');
        $this->addSql('ALTER TABLE cobro CHANGE alumno_id cliente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cobro ADD CONSTRAINT FK_F0A26526DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('CREATE INDEX IDX_F0A26526DE734E51 ON cobro (cliente_id)');
        $this->addSql('ALTER TABLE persona CHANGE esalumno escliente TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cobro DROP FOREIGN KEY FK_F0A26526DE734E51');
        $this->addSql('CREATE TABLE alumno (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, telefono VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fecha_nac DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE alumno ADD CONSTRAINT FK_1435D52DBF396750 FOREIGN KEY (id) REFERENCES usuario (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP INDEX IDX_F0A26526DE734E51 ON cobro');
        $this->addSql('ALTER TABLE cobro CHANGE cliente_id alumno_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cobro ADD CONSTRAINT FK_F0A26526FC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F0A26526FC28E5EE ON cobro (alumno_id)');
        $this->addSql('ALTER TABLE persona CHANGE escliente esalumno TINYINT(1) NOT NULL');
    }
}
