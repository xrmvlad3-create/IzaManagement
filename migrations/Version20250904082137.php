<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904082137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE clinical_case_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE clinical_case_media_asset DROP CONSTRAINT fk_658333acabb37f3');
        $this->addSql('ALTER TABLE clinical_case_media_asset DROP CONSTRAINT fk_658333acba2691f');
        $this->addSql('DROP TABLE clinical_case_media_asset');
        $this->addSql('ALTER TABLE aiconsultation ALTER clinical_case_id TYPE INT');
        $this->addSql('COMMENT ON COLUMN aiconsultation.clinical_case_id IS NULL');
        $this->addSql('ALTER TABLE clinical_case DROP CONSTRAINT fk_6afe4b30f675f31b');
        $this->addSql('DROP INDEX idx_6afe4b30f675f31b');
        $this->addSql('ALTER TABLE clinical_case ADD symptoms TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD treatment_protocol TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD procedure_codes JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD guides TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD media JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD audit_log TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE clinical_case DROP patient_meta');
        $this->addSql('ALTER TABLE clinical_case DROP tags');
        $this->addSql('ALTER TABLE clinical_case ALTER id TYPE INT');
        $this->addSql('ALTER TABLE clinical_case RENAME COLUMN author_id TO created_by_id');
        $this->addSql('ALTER TABLE clinical_case RENAME COLUMN title TO name');
        $this->addSql('ALTER TABLE clinical_case RENAME COLUMN notes TO description');
        $this->addSql('COMMENT ON COLUMN clinical_case.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clinical_case.id IS NULL');
        $this->addSql('ALTER TABLE clinical_case ADD CONSTRAINT FK_6AFE4B30B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6AFE4B30B03A8386 ON clinical_case (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE clinical_case_id_seq CASCADE');
        $this->addSql('CREATE TABLE clinical_case_media_asset (clinical_case_id UUID NOT NULL, media_asset_id UUID NOT NULL, PRIMARY KEY(clinical_case_id, media_asset_id))');
        $this->addSql('CREATE INDEX idx_658333acabb37f3 ON clinical_case_media_asset (media_asset_id)');
        $this->addSql('CREATE INDEX idx_658333acba2691f ON clinical_case_media_asset (clinical_case_id)');
        $this->addSql('COMMENT ON COLUMN clinical_case_media_asset.clinical_case_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clinical_case_media_asset.media_asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE clinical_case_media_asset ADD CONSTRAINT fk_658333acabb37f3 FOREIGN KEY (media_asset_id) REFERENCES media_asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clinical_case_media_asset ADD CONSTRAINT fk_658333acba2691f FOREIGN KEY (clinical_case_id) REFERENCES clinical_case (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clinical_case DROP CONSTRAINT FK_6AFE4B30B03A8386');
        $this->addSql('DROP INDEX IDX_6AFE4B30B03A8386');
        $this->addSql('ALTER TABLE clinical_case ADD notes TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD patient_meta JSON NOT NULL');
        $this->addSql('ALTER TABLE clinical_case ADD tags JSON NOT NULL');
        $this->addSql('ALTER TABLE clinical_case DROP description');
        $this->addSql('ALTER TABLE clinical_case DROP symptoms');
        $this->addSql('ALTER TABLE clinical_case DROP treatment_protocol');
        $this->addSql('ALTER TABLE clinical_case DROP procedure_codes');
        $this->addSql('ALTER TABLE clinical_case DROP guides');
        $this->addSql('ALTER TABLE clinical_case DROP media');
        $this->addSql('ALTER TABLE clinical_case DROP audit_log');
        $this->addSql('ALTER TABLE clinical_case DROP updated_at');
        $this->addSql('ALTER TABLE clinical_case ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE clinical_case RENAME COLUMN created_by_id TO author_id');
        $this->addSql('ALTER TABLE clinical_case RENAME COLUMN name TO title');
        $this->addSql('COMMENT ON COLUMN clinical_case.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE clinical_case ADD CONSTRAINT fk_6afe4b30f675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6afe4b30f675f31b ON clinical_case (author_id)');
        $this->addSql('ALTER TABLE aiconsultation ALTER clinical_case_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN aiconsultation.clinical_case_id IS \'(DC2Type:uuid)\'');
    }
}
