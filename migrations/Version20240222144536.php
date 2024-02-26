<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222144536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ev_recharge_point (id INT AUTO_INCREMENT NOT NULL, type_of_charging VARCHAR(255) DEFAULT NULL, power_kw VARCHAR(255) DEFAULT NULL, level VARCHAR(255) DEFAULT NULL, is_fast_charge_capable VARCHAR(255) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_DACB90ABD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ev_information ADD date_created DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', DROP type_of_charging');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ev_recharge_point');
        $this->addSql('ALTER TABLE ev_information ADD type_of_charging VARCHAR(255) DEFAULT NULL, DROP date_created');
    }
}
