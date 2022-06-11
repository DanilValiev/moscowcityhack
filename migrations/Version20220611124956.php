<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220611124956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE industrial_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE industrial (id INT NOT NULL, title VARCHAR(255) NOT NULL, external_id INT NOT NULL, photo VARCHAR(255) DEFAULT NULL, adress TEXT NOT NULL, uri VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE production_industrial (production_id INT NOT NULL, industrial_id INT NOT NULL, PRIMARY KEY(production_id, industrial_id))');
        $this->addSql('CREATE INDEX IDX_E9BF3EE1ECC6147F ON production_industrial (production_id)');
        $this->addSql('CREATE INDEX IDX_E9BF3EE14E8677CA ON production_industrial (industrial_id)');
        $this->addSql('ALTER TABLE production_industrial ADD CONSTRAINT FK_E9BF3EE1ECC6147F FOREIGN KEY (production_id) REFERENCES production (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE production_industrial ADD CONSTRAINT FK_E9BF3EE14E8677CA FOREIGN KEY (industrial_id) REFERENCES industrial (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD industry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD gost TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD odkp2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD tnved VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD characteristics JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD okei_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2B19A734 FOREIGN KEY (industry_id) REFERENCES industrial (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD2B19A734 ON product (industry_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD2B19A734');
        $this->addSql('ALTER TABLE production_industrial DROP CONSTRAINT FK_E9BF3EE14E8677CA');
        $this->addSql('DROP SEQUENCE industrial_id_seq CASCADE');
        $this->addSql('DROP TABLE industrial');
        $this->addSql('DROP TABLE production_industrial');
        $this->addSql('DROP INDEX IDX_D34A04AD2B19A734');
        $this->addSql('ALTER TABLE product DROP industry_id');
        $this->addSql('ALTER TABLE product DROP description');
        $this->addSql('ALTER TABLE product DROP gost');
        $this->addSql('ALTER TABLE product DROP odkp2');
        $this->addSql('ALTER TABLE product DROP tnved');
        $this->addSql('ALTER TABLE product DROP characteristics');
        $this->addSql('ALTER TABLE product DROP okei_id');
    }
}
