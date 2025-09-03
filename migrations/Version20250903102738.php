<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903102738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aiconsultation (id UUID NOT NULL, clinical_case_id UUID DEFAULT NULL, user_id UUID NOT NULL, question TEXT NOT NULL, model VARCHAR(100) NOT NULL, inputs_meta JSON NOT NULL, raw_prompt TEXT NOT NULL, ai_response JSON NOT NULL, disclaimer_accepted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A49C84E7BA2691F ON aiconsultation (clinical_case_id)');
        $this->addSql('CREATE INDEX IDX_A49C84E7A76ED395 ON aiconsultation (user_id)');
        $this->addSql('COMMENT ON COLUMN aiconsultation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN aiconsultation.clinical_case_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN aiconsultation.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN aiconsultation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE audit_log (id UUID NOT NULL, user_id UUID DEFAULT NULL, action VARCHAR(255) NOT NULL, entity VARCHAR(255) NOT NULL, entity_id VARCHAR(255) DEFAULT NULL, payload JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F6E1C0F5A76ED395 ON audit_log (user_id)');
        $this->addSql('COMMENT ON COLUMN audit_log.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_log.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_log.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE clinical_case (id UUID NOT NULL, author_id UUID NOT NULL, title VARCHAR(255) NOT NULL, notes TEXT DEFAULT NULL, patient_meta JSON NOT NULL, tags JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6AFE4B30F675F31B ON clinical_case (author_id)');
        $this->addSql('COMMENT ON COLUMN clinical_case.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clinical_case.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clinical_case.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE clinical_case_media_asset (clinical_case_id UUID NOT NULL, media_asset_id UUID NOT NULL, PRIMARY KEY(clinical_case_id, media_asset_id))');
        $this->addSql('CREATE INDEX IDX_658333ACBA2691F ON clinical_case_media_asset (clinical_case_id)');
        $this->addSql('CREATE INDEX IDX_658333ACABB37F3 ON clinical_case_media_asset (media_asset_id)');
        $this->addSql('COMMENT ON COLUMN clinical_case_media_asset.clinical_case_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clinical_case_media_asset.media_asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE course (id UUID NOT NULL, created_by_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, sections JSON DEFAULT NULL, status VARCHAR(255) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, ce_credits INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_169E6FB9B03A8386 ON course (created_by_id)');
        $this->addSql('COMMENT ON COLUMN course.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN course.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN course.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN course.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE derm_conditions (id UUID NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, summary TEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87102E37989D9B62 ON derm_conditions (slug)');
        $this->addSql('COMMENT ON COLUMN derm_conditions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE doctor_profile (id UUID NOT NULL, user_id UUID NOT NULL, hospital VARCHAR(255) DEFAULT NULL, license_number VARCHAR(255) DEFAULT NULL, specialty VARCHAR(255) DEFAULT NULL, preferences JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_12FAC9A2A76ED395 ON doctor_profile (user_id)');
        $this->addSql('COMMENT ON COLUMN doctor_profile.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN doctor_profile.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE enrollment (id UUID NOT NULL, course_id UUID NOT NULL, user_id UUID NOT NULL, status VARCHAR(255) NOT NULL, score DOUBLE PRECISION DEFAULT NULL, certificate_url VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1591CC992 ON enrollment (course_id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1A76ED395 ON enrollment (user_id)');
        $this->addSql('COMMENT ON COLUMN enrollment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN enrollment.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN enrollment.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN enrollment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE lesson (id UUID NOT NULL, course_id UUID NOT NULL, title VARCHAR(255) NOT NULL, body TEXT DEFAULT NULL, video_url VARCHAR(255) DEFAULT NULL, attachments JSON DEFAULT NULL, order_index INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F87474F3591CC992 ON lesson (course_id)');
        $this->addSql('COMMENT ON COLUMN lesson.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lesson.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE media_asset (id UUID NOT NULL, owner_id UUID DEFAULT NULL, type VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, mime VARCHAR(100) NOT NULL, size INT NOT NULL, storage_path VARCHAR(1024) NOT NULL, sha256 VARCHAR(64) NOT NULL, meta JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DB69EED7E3C61F9 ON media_asset (owner_id)');
        $this->addSql('COMMENT ON COLUMN media_asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN media_asset.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN media_asset.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE protocol (id UUID NOT NULL, condition_id UUID NOT NULL, name VARCHAR(255) NOT NULL, intent VARCHAR(255) NOT NULL, audience VARCHAR(255) DEFAULT NULL, steps JSON NOT NULL, meds JSON DEFAULT NULL, contraindications TEXT DEFAULT NULL, evidence_level VARCHAR(100) DEFAULT NULL, attachments JSON DEFAULT NULL, status VARCHAR(255) NOT NULL, version INT DEFAULT 1 NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C8C0BC4C887793B6 ON protocol (condition_id)');
        $this->addSql('COMMENT ON COLUMN protocol.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN protocol.condition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN protocol.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE aiconsultation ADD CONSTRAINT FK_A49C84E7BA2691F FOREIGN KEY (clinical_case_id) REFERENCES clinical_case (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE aiconsultation ADD CONSTRAINT FK_A49C84E7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clinical_case ADD CONSTRAINT FK_6AFE4B30F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clinical_case_media_asset ADD CONSTRAINT FK_658333ACBA2691F FOREIGN KEY (clinical_case_id) REFERENCES clinical_case (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clinical_case_media_asset ADD CONSTRAINT FK_658333ACABB37F3 FOREIGN KEY (media_asset_id) REFERENCES media_asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE doctor_profile ADD CONSTRAINT FK_12FAC9A2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media_asset ADD CONSTRAINT FK_1DB69EED7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE protocol ADD CONSTRAINT FK_C8C0BC4C887793B6 FOREIGN KEY (condition_id) REFERENCES derm_conditions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE aiconsultation DROP CONSTRAINT FK_A49C84E7BA2691F');
        $this->addSql('ALTER TABLE aiconsultation DROP CONSTRAINT FK_A49C84E7A76ED395');
        $this->addSql('ALTER TABLE audit_log DROP CONSTRAINT FK_F6E1C0F5A76ED395');
        $this->addSql('ALTER TABLE clinical_case DROP CONSTRAINT FK_6AFE4B30F675F31B');
        $this->addSql('ALTER TABLE clinical_case_media_asset DROP CONSTRAINT FK_658333ACBA2691F');
        $this->addSql('ALTER TABLE clinical_case_media_asset DROP CONSTRAINT FK_658333ACABB37F3');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9B03A8386');
        $this->addSql('ALTER TABLE doctor_profile DROP CONSTRAINT FK_12FAC9A2A76ED395');
        $this->addSql('ALTER TABLE enrollment DROP CONSTRAINT FK_DBDCD7E1591CC992');
        $this->addSql('ALTER TABLE enrollment DROP CONSTRAINT FK_DBDCD7E1A76ED395');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F3591CC992');
        $this->addSql('ALTER TABLE media_asset DROP CONSTRAINT FK_1DB69EED7E3C61F9');
        $this->addSql('ALTER TABLE protocol DROP CONSTRAINT FK_C8C0BC4C887793B6');
        $this->addSql('DROP TABLE aiconsultation');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE clinical_case');
        $this->addSql('DROP TABLE clinical_case_media_asset');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE derm_conditions');
        $this->addSql('DROP TABLE doctor_profile');
        $this->addSql('DROP TABLE enrollment');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE media_asset');
        $this->addSql('DROP TABLE protocol');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
