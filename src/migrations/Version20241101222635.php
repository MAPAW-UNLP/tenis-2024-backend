<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101222635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrador (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alquiler (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, telefono VARCHAR(15) NOT NULL, reserva_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cancha (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(20) NOT NULL, tipo VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clases (id INT AUTO_INCREMENT NOT NULL, tipo VARCHAR(20) NOT NULL, importe INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, telefono VARCHAR(15) NOT NULL, fecha_nac DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cobro (id INT AUTO_INCREMENT NOT NULL, cliente_id INT DEFAULT NULL, id_persona INT DEFAULT NULL, id_tipo_clase INT DEFAULT NULL, monto DOUBLE PRECISION NOT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, concepto VARCHAR(255) NOT NULL, descripcion VARCHAR(100) DEFAULT NULL, INDEX IDX_F0A26526DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constancia_mantenimiento (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cuenta (id INT AUTO_INCREMENT NOT NULL, persona_id INT NOT NULL, importe INT NOT NULL, fecha DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estado (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grupo (id INT AUTO_INCREMENT NOT NULL, reserva_id INT NOT NULL, persona_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horario_disponible (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, hora_ini TIME NOT NULL, hora_fin TIME NOT NULL, profesor_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pagos (id INT AUTO_INCREMENT NOT NULL, profesor_id INT DEFAULT NULL, id_persona INT DEFAULT NULL, id_tipo_clase INT DEFAULT NULL, monto DOUBLE PRECISION NOT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, motivo VARCHAR(255) NOT NULL, descripcion VARCHAR(100) DEFAULT NULL, cantidad INT DEFAULT NULL, INDEX IDX_DA9B0DFFE52BD977 (profesor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE periodo_ausencia (id INT AUTO_INCREMENT NOT NULL, fecha_ini DATE NOT NULL, fecha_fin DATE NOT NULL, motivo LONGTEXT NOT NULL, profesor_id INT NOT NULL, estado_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persona (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, telefono VARCHAR(15) NOT NULL, fechanac DATE DEFAULT NULL, escliente TINYINT(1) NOT NULL, visible TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profesor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, telefono VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE replicas (id INT AUTO_INCREMENT NOT NULL, id_reserva INT NOT NULL, ultimo_mes INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reserva (id INT AUTO_INCREMENT NOT NULL, cancha_id INT NOT NULL, fecha DATE NOT NULL, hora_ini TIME NOT NULL, hora_fin TIME NOT NULL, persona_id INT DEFAULT NULL, replica TINYINT(1) NOT NULL, estado_id INT NOT NULL, id_tipo_clase INT DEFAULT NULL, pago_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suspension_clase (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, profesor_id INT NOT NULL, estado_id INT NOT NULL, reserva_id INT NOT NULL, motivo LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, fechapagos DATE DEFAULT NULL, fechareplica DATE DEFAULT NULL, rol_por_defecto VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrador ADD CONSTRAINT FK_44F9A521BF396750 FOREIGN KEY (id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25BF396750 FOREIGN KEY (id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE cobro ADD CONSTRAINT FK_F0A26526DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE pagos ADD CONSTRAINT FK_DA9B0DFFE52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
        $this->addSql('ALTER TABLE profesor ADD CONSTRAINT FK_5B7406D9BF396750 FOREIGN KEY (id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cobro DROP FOREIGN KEY FK_F0A26526DE734E51');
        $this->addSql('ALTER TABLE pagos DROP FOREIGN KEY FK_DA9B0DFFE52BD977');
        $this->addSql('ALTER TABLE administrador DROP FOREIGN KEY FK_44F9A521BF396750');
        $this->addSql('ALTER TABLE cliente DROP FOREIGN KEY FK_F41C9B25BF396750');
        $this->addSql('ALTER TABLE profesor DROP FOREIGN KEY FK_5B7406D9BF396750');
        $this->addSql('DROP TABLE administrador');
        $this->addSql('DROP TABLE alquiler');
        $this->addSql('DROP TABLE cancha');
        $this->addSql('DROP TABLE clases');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE cobro');
        $this->addSql('DROP TABLE constancia_mantenimiento');
        $this->addSql('DROP TABLE cuenta');
        $this->addSql('DROP TABLE estado');
        $this->addSql('DROP TABLE grupo');
        $this->addSql('DROP TABLE horario_disponible');
        $this->addSql('DROP TABLE pagos');
        $this->addSql('DROP TABLE periodo_ausencia');
        $this->addSql('DROP TABLE persona');
        $this->addSql('DROP TABLE profesor');
        $this->addSql('DROP TABLE replicas');
        $this->addSql('DROP TABLE reserva');
        $this->addSql('DROP TABLE suspension_clase');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
