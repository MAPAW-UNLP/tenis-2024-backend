<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003161810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrador DROP user, DROP password');
        $this->addSql('ALTER TABLE alumno ADD CONSTRAINT FK_1435D52DBF396750 FOREIGN KEY (id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario ADD rol_por_defecto VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrador ADD user VARCHAR(30) NOT NULL, ADD password VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE alumno DROP FOREIGN KEY FK_1435D52DBF396750');
        $this->addSql('ALTER TABLE usuario DROP rol_por_defecto');
    }
}
