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
        return 'Modify doctor_profile table structure with proper type conversion';
    }

    public function up(Schema $schema): void
    {
        // Create sequence for integer IDs
        $this->addSql('CREATE SEQUENCE doctor_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        // Remove foreign key constraint temporarily
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT FK_12FAC9A2A76ED395');

        // Add new integer column for ID
        $this->addSql('ALTER TABLE doctor_profile ADD COLUMN new_id INT');

        // Populate new_id with values from the sequence
        $this->addSql('UPDATE doctor_profile SET new_id = nextval(\'doctor_profile_id_seq\')');

        // Drop the old UUID primary key
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT doctor_profile_pkey');

        // Change new_id to be the primary key and set up proper constraints
        $this->addSql('ALTER TABLE doctor_profile ALTER COLUMN new_id SET NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD PRIMARY KEY (new_id)');

        // Remove the old UUID column
        $this->addSql('ALTER TABLE doctor_profile DROP COLUMN id');

        // Rename new_id to id
        $this->addSql('ALTER TABLE doctor_profile RENAME COLUMN new_id TO id');

        // Add the specialties column
        $this->addSql('ALTER TABLE doctor_profile ADD specialties JSON NOT NULL');

        // Remove unnecessary columns
        $this->addSql('ALTER TABLE doctor_profile DROP hospital');
        $this->addSql('ALTER TABLE doctor_profile DROP license_number');
        $this->addSql('ALTER TABLE doctor_profile DROP specialty');
        $this->addSql('ALTER TABLE doctor_profile DROP preferences');
        $this->addSql('ALTER TABLE doctor_profile DROP created_at');
        $this->addSql('ALTER TABLE doctor_profile DROP updated_at');

        // Re-add foreign key constraint
        $this->addSql('ALTER TABLE doctor_profile ADD CONSTRAINT FK_12FAC9A2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Set the default value for the sequence
        $this->addSql('ALTER SEQUENCE doctor_profile_id_seq OWNED BY doctor_profile.id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');

        // Remove foreign key constraint
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT fk_12fac9a2a76ed395');

        // Add back the removed columns
        $this->addSql('ALTER TABLE doctor_profile ADD hospital VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD license_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD specialty VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD preferences JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');

        // Remove specialties column
        $this->addSql('ALTER TABLE doctor_profile DROP specialties');

        // Add UUID column
        $this->addSql('ALTER TABLE doctor_profile ADD COLUMN id UUID');

        // Generate UUID values (this is a simplified approach)
        $this->addSql('UPDATE doctor_profile SET id = gen_random_uuid()');

        // Set UUID as primary key
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT doctor_profile_pkey');
        $this->addSql('ALTER TABLE doctor_profile ALTER COLUMN id SET NOT NULL');
        $this->addSql('ALTER TABLE doctor_profile ADD PRIMARY KEY (id)');

        // Drop sequence
        $this->addSql('DROP SEQUENCE doctor_profile_id_seq CASCADE');

        // Re-add foreign key constraint
        $this->addSql('ALTER TABLE doctor_profile ADD CONSTRAINT fk_12fac9a2a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Add comments
        $this->addSql('COMMENT ON COLUMN doctor_profile.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.id IS \'(DC2Type:uuid)\'');
    }
}
