<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609142126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE contact_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contact_info (id INT NOT NULL, production_id INT NOT NULL, director_fio VARCHAR(255) DEFAULT NULL, contact_fio VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_fax VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E376B3A8ECC6147F ON contact_info (production_id)');
        $this->addSql('ALTER TABLE contact_info ADD CONSTRAINT FK_E376B3A8ECC6147F FOREIGN KEY (production_id) REFERENCES production (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE contact_info_id_seq CASCADE');
        $this->addSql('DROP TABLE contact_info');
    }
}
