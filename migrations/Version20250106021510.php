<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106021510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book ADD genre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book DROP nombre');
        $this->addSql('ALTER TABLE book DROP genero');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book ADD nombre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book ADD genero VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book DROP title');
        $this->addSql('ALTER TABLE book DROP genre');
    }
}
