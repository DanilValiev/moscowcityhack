<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609144051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE production ADD region_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E098260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3EDB1E098260155 ON production (region_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production DROP CONSTRAINT FK_D3EDB1E098260155');
        $this->addSql('DROP INDEX IDX_D3EDB1E098260155');
        $this->addSql('ALTER TABLE production DROP region_id');
    }
}
