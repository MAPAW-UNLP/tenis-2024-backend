<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028192404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_alquiler (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(50) NOT NULL, importe INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO clases (tipo, importe) values ("GRUPAL", 50)');
        $this->addSql('INSERT INTO item_alquiler (description, importe) values ("RAQUETA", 2000)');
        $this->addSql('INSERT INTO item_alquiler (description, importe) values ("PELOTA", 100)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE item_alquiler');
    }
}
