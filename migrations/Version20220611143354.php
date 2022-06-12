<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220611143354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE production ADD manual BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE production ALTER ogrn DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production DROP manual');
        $this->addSql('ALTER TABLE production ALTER ogrn SET NOT NULL');
    }
}
