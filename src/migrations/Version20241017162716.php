<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017162716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proveedor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, telefono VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pagos ADD proveedor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pagos ADD CONSTRAINT FK_DA9B0DFFCB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)');
        $this->addSql('CREATE INDEX IDX_DA9B0DFFCB305D73 ON pagos (proveedor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pagos DROP FOREIGN KEY FK_DA9B0DFFCB305D73');
        $this->addSql('DROP TABLE proveedor');
        $this->addSql('DROP INDEX IDX_DA9B0DFFCB305D73 ON pagos');
        $this->addSql('ALTER TABLE pagos DROP proveedor_id');
    }
}
