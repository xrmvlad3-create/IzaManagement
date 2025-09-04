<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904131341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE doctor_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT FK_12FAC9A2A76ED395');
        $this->addSql('ALTER TABLE doctor_profile ADD specialties JSON NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile DROP hospital');
        $this->addSql('ALTER TABLE doctor_profile DROP license_number');
        $this->addSql('ALTER TABLE doctor_profile DROP specialty');
        $this->addSql('ALTER TABLE doctor_profile DROP preferences');
        $this->addSql('ALTER TABLE doctor_profile DROP created_at');
        $this->addSql('ALTER TABLE doctor_profile DROP updated_at');
        $this->addSql('ALTER TABLE doctor_profile ALTER id TYPE INT');
        $this->addSql('COMMENT ON COLUMN doctor_profile.id IS NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD CONSTRAINT FK_12FAC9A2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE doctor_profile_id_seq CASCADE');
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT fk_12fac9a2a76ed395');
        $this->addSql('ALTER TABLE doctor_profile ADD hospital VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD license_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD specialty VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD preferences JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile DROP specialties');
        $this->addSql('ALTER TABLE doctor_profile ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN doctor_profile.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE doctor_profile ADD CONSTRAINT fk_12fac9a2a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
