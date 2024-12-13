<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212040051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prestamo (id SERIAL NOT NULL, student_id INT NOT NULL, book_id INT NOT NULL, fecha_prestamo INT NOT NULL, fecha_devolucion INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F4D874F2CB944F1A ON prestamo (student_id)');
        $this->addSql('CREATE INDEX IDX_F4D874F216A2B381 ON prestamo (book_id)');
        $this->addSql('ALTER TABLE prestamo ADD CONSTRAINT FK_F4D874F2CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestamo ADD CONSTRAINT FK_F4D874F216A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE prestamo DROP CONSTRAINT FK_F4D874F2CB944F1A');
        $this->addSql('ALTER TABLE prestamo DROP CONSTRAINT FK_F4D874F216A2B381');
        $this->addSql('DROP TABLE prestamo');
    }
}
