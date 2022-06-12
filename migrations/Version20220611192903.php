<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611192903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production ADD fin_report JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD staturory_capital VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE production DROP "statutoryСcapital"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE production ADD "statutoryСcapital" INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production DROP fin_report');
        $this->addSql('ALTER TABLE production DROP staturory_capital');
    }
}
