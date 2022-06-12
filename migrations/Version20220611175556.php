<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220611175556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE odkv_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE odkv (id INT NOT NULL, code VARCHAR(255) NOT NULL, title TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE production_odkv (production_id INT NOT NULL, odkv_id INT NOT NULL, PRIMARY KEY(production_id, odkv_id))');
        $this->addSql('CREATE INDEX IDX_E9A076C4ECC6147F ON production_odkv (production_id)');
        $this->addSql('CREATE INDEX IDX_E9A076C44A410B00 ON production_odkv (odkv_id)');
        $this->addSql('ALTER TABLE production_odkv ADD CONSTRAINT FK_E9A076C4ECC6147F FOREIGN KEY (production_id) REFERENCES production (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE production_odkv ADD CONSTRAINT FK_E9A076C44A410B00 FOREIGN KEY (odkv_id) REFERENCES odkv (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE production ADD odvk_primary_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD statutoryСcapital INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD support JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD company_reg_data VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E03ED7A30C FOREIGN KEY (odvk_primary_id) REFERENCES odkv (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3EDB1E03ED7A30C ON production (odvk_primary_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production DROP CONSTRAINT FK_D3EDB1E03ED7A30C');
        $this->addSql('ALTER TABLE production_odkv DROP CONSTRAINT FK_E9A076C44A410B00');
        $this->addSql('DROP SEQUENCE odkv_id_seq CASCADE');
        $this->addSql('DROP TABLE odkv');
        $this->addSql('DROP TABLE production_odkv');
        $this->addSql('DROP INDEX IDX_D3EDB1E03ED7A30C');
        $this->addSql('ALTER TABLE production DROP odvk_primary_id');
        $this->addSql('ALTER TABLE production DROP statutoryСcapital');
        $this->addSql('ALTER TABLE production DROP support');
        $this->addSql('ALTER TABLE production DROP company_reg_data');
    }
}
