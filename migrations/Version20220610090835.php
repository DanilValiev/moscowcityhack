<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220610090835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ALTER title TYPE TEXT');
        $this->addSql('ALTER TABLE product ALTER title DROP DEFAULT');
        $this->addSql('ALTER TABLE production ALTER title TYPE TEXT');
        $this->addSql('ALTER TABLE production ALTER title DROP DEFAULT');
        $this->addSql('ALTER TABLE production ALTER address TYPE TEXT');
        $this->addSql('ALTER TABLE production ALTER address DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE production ALTER title DROP DEFAULT');
        $this->addSql('ALTER TABLE production ALTER address TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE production ALTER address DROP DEFAULT');
        $this->addSql('ALTER TABLE product ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product ALTER title DROP DEFAULT');
    }
}
