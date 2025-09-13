<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250913105816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clinical_case (id INT AUTO_INCREMENT NOT NULL, speciality_id INT DEFAULT NULL, name VARCHAR(500) NOT NULL, description VARCHAR(50000) DEFAULT NULL, INDEX IDX_6AFE4B303B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clinical_case ADD CONSTRAINT FK_6AFE4B303B5A08D7 FOREIGN KEY (speciality_id) REFERENCES specialty (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clinical_case DROP FOREIGN KEY FK_6AFE4B303B5A08D7');
        $this->addSql('DROP TABLE clinical_case');
    }
}
