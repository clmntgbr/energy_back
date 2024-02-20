<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220081301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ev_information CHANGE number_recharge_point number_recharge_point VARCHAR(255) DEFAULT NULL, CHANGE maximum_power maximum_power VARCHAR(255) DEFAULT NULL, CHANGE type_of_charging type_of_charging VARCHAR(255) DEFAULT NULL, CHANGE charging_access charging_access VARCHAR(255) DEFAULT NULL, CHANGE accessibility accessibility LONGTEXT DEFAULT NULL, CHANGE observations observations LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ev_information CHANGE number_recharge_point number_recharge_point VARCHAR(255) NOT NULL, CHANGE maximum_power maximum_power VARCHAR(255) NOT NULL, CHANGE type_of_charging type_of_charging VARCHAR(255) NOT NULL, CHANGE charging_access charging_access VARCHAR(255) NOT NULL, CHANGE accessibility accessibility VARCHAR(255) NOT NULL, CHANGE observations observations VARCHAR(255) NOT NULL');
    }
}
