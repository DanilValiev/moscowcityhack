<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609152048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE production ALTER ogrn TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE production ALTER ogrn DROP DEFAULT');
        $this->addSql('ALTER TABLE production ALTER inn TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE production ALTER inn DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production ALTER ogrn TYPE INT');
        $this->addSql('ALTER TABLE production ALTER ogrn DROP DEFAULT');
        $this->addSql('ALTER TABLE production ALTER inn TYPE INT');
        $this->addSql('ALTER TABLE production ALTER inn DROP DEFAULT');
    }
}
