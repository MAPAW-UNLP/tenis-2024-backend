<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909130716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO usuario (username, password, rol) VALUES ("admin", "admin", "admin")');
        $this->addSql('INSERT INTO clases (tipo, importe) values ("INDIVIDUAL", 100)');
        $this->addSql('INSERT INTO clases (tipo, importe) values ("GRUPAL", 50)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM usuario WHERE username = "admin"');
        $this->addSql('DELETE FROM clases WHERE tipo = "INDIVIDUAL"');
        $this->addSql('DELETE FROM clases WHERE tipo = "GRUPAL"');
    }
}
