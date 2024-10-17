<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003161811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Actualizar el rol por defecto del usuario 'admin'
        $this->addSql('UPDATE usuario SET rol_por_defecto = "ROLE_ADMIN" WHERE username = "admin"');

        // Obtener el id del usuario 'admin'
        $this->addSql('SET @userId = (SELECT id FROM usuario WHERE username = "admin")');

        // Insertar en la tabla administrador usando el id del usuario
        $this->addSql('INSERT INTO administrador (id) VALUES (@userId)');
    }

    public function down(Schema $schema): void
    {
        // Eliminar el registro de la tabla administrador correspondiente al usuario 'admin'
        $this->addSql('DELETE FROM administrador WHERE id = (SELECT id FROM usuario WHERE username = "admin")');

        // Revertir la actualización del campo rol_por_defecto en la tabla usuario
        $this->addSql('UPDATE usuario SET rol = "admin" WHERE username = "admin"');
    }


}
