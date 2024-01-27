<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127191757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE energy_station_energy_service (energy_station_id INT NOT NULL, energy_service_id INT NOT NULL, INDEX IDX_4547F5F5CECD2951 (energy_station_id), INDEX IDX_4547F5F522C3282 (energy_service_id), PRIMARY KEY(energy_station_id, energy_service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_station_brand (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE energy_station_energy_service ADD CONSTRAINT FK_4547F5F5CECD2951 FOREIGN KEY (energy_station_id) REFERENCES energy_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE energy_station_energy_service ADD CONSTRAINT FK_4547F5F522C3282 FOREIGN KEY (energy_service_id) REFERENCES energy_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE energy_station ADD address_id INT NOT NULL, ADD google_place_id INT NOT NULL, ADD energy_station_brand_id INT DEFAULT NULL, ADD pop VARCHAR(10) NOT NULL, ADD energy_station_id VARCHAR(20) NOT NULL, ADD name VARCHAR(255) DEFAULT NULL, ADD statuses JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD status VARCHAR(255) DEFAULT NULL, ADD has_energy_station_brand_verified TINYINT(1) DEFAULT NULL, ADD closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD element JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD hash VARCHAR(255) DEFAULT NULL, ADD last_energy_prices JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD previous_energy_prices JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD max_retry_position_stack INT NOT NULL, ADD max_retry_text_search INT NOT NULL, ADD max_retry_place_details INT NOT NULL, ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_original_name VARCHAR(255) DEFAULT NULL, ADD image_mime_type VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265983C031 FOREIGN KEY (google_place_id) REFERENCES google_place (id)');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265345D6612 FOREIGN KEY (energy_station_brand_id) REFERENCES energy_station_brand (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA071265D17F50A6 ON energy_station (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA071265F5B7AF75 ON energy_station (address_id)');
        $this->addSql('CREATE INDEX IDX_DA071265983C031 ON energy_station (google_place_id)');
        $this->addSql('CREATE INDEX IDX_DA071265345D6612 ON energy_station (energy_station_brand_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265345D6612');
        $this->addSql('ALTER TABLE energy_station_energy_service DROP FOREIGN KEY FK_4547F5F5CECD2951');
        $this->addSql('ALTER TABLE energy_station_energy_service DROP FOREIGN KEY FK_4547F5F522C3282');
        $this->addSql('DROP TABLE energy_station_energy_service');
        $this->addSql('DROP TABLE energy_station_brand');
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265F5B7AF75');
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265983C031');
        $this->addSql('DROP INDEX UNIQ_DA071265D17F50A6 ON energy_station');
        $this->addSql('DROP INDEX UNIQ_DA071265F5B7AF75 ON energy_station');
        $this->addSql('DROP INDEX IDX_DA071265983C031 ON energy_station');
        $this->addSql('DROP INDEX IDX_DA071265345D6612 ON energy_station');
        $this->addSql('ALTER TABLE energy_station DROP address_id, DROP google_place_id, DROP energy_station_brand_id, DROP pop, DROP energy_station_id, DROP name, DROP statuses, DROP status, DROP has_energy_station_brand_verified, DROP closed_at, DROP element, DROP hash, DROP last_energy_prices, DROP previous_energy_prices, DROP max_retry_position_stack, DROP max_retry_text_search, DROP max_retry_place_details, DROP uuid, DROP created_at, DROP updated_at, DROP created_by, DROP updated_by, DROP image_name, DROP image_original_name, DROP image_mime_type, DROP image_size, DROP image_dimensions');
    }
}
