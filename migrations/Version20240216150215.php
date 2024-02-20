<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216150215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, vicinity VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, number VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, longitude VARCHAR(50) DEFAULT NULL, latitude VARCHAR(50) DEFAULT NULL, position_stack_api_result JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D4E6F81D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6956883FD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_price (id INT AUTO_INCREMENT NOT NULL, energy_station_id INT NOT NULL, energy_type_id INT NOT NULL, currency_id INT NOT NULL, value INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_timestamp INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_72F11792D17F50A6 (uuid), INDEX IDX_72F11792CECD2951 (energy_station_id), INDEX IDX_72F1179280726647 (energy_type_id), INDEX IDX_72F1179238248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_service (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A4A37006D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_station (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, google_place_id INT NOT NULL, energy_station_brand_id INT DEFAULT NULL, pop VARCHAR(255) NOT NULL, type VARCHAR(5) NOT NULL, energy_station_id VARCHAR(100) NOT NULL, name VARCHAR(255) DEFAULT NULL, statuses JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) DEFAULT NULL, has_energy_station_brand_verified TINYINT(1) DEFAULT NULL, closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', element JSON NOT NULL COMMENT \'(DC2Type:json)\', hash VARCHAR(255) DEFAULT NULL, services JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', last_energy_prices JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', previous_energy_prices JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', max_retry_position_stack INT NOT NULL, max_retry_text_search INT NOT NULL, max_retry_place_details INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_DA071265D17F50A6 (uuid), UNIQUE INDEX UNIQ_DA071265F5B7AF75 (address_id), INDEX IDX_DA071265983C031 (google_place_id), INDEX IDX_DA071265345D6612 (energy_station_brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_station_brand (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', image_low_name VARCHAR(255) DEFAULT NULL, image_low_original_name VARCHAR(255) DEFAULT NULL, image_low_mime_type VARCHAR(255) DEFAULT NULL, image_low_size INT DEFAULT NULL, image_low_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_C9BAC790D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE energy_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(5) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_84E131ED17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_place (id INT AUTO_INCREMENT NOT NULL, google_id VARCHAR(15) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, place_id VARCHAR(50) DEFAULT NULL, compound_code VARCHAR(50) DEFAULT NULL, global_code VARCHAR(50) DEFAULT NULL, google_rating VARCHAR(10) DEFAULT NULL, rating VARCHAR(10) DEFAULT NULL, user_ratings_total VARCHAR(10) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, reference VARCHAR(50) DEFAULT NULL, wheelchair_accessible_entrance VARCHAR(255) DEFAULT NULL, business_status VARCHAR(50) DEFAULT NULL, opening_hours JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', textsearch_api_result JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', place_details_api_result JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_EDF05AC2D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(200) NOT NULL, username VARCHAR(200) NOT NULL, name VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_enable TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F11792CECD2951 FOREIGN KEY (energy_station_id) REFERENCES energy_station (id)');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F1179280726647 FOREIGN KEY (energy_type_id) REFERENCES energy_type (id)');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F1179238248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265983C031 FOREIGN KEY (google_place_id) REFERENCES google_place (id)');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265345D6612 FOREIGN KEY (energy_station_brand_id) REFERENCES energy_station_brand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F11792CECD2951');
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F1179280726647');
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F1179238248176');
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265F5B7AF75');
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265983C031');
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265345D6612');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE energy_price');
        $this->addSql('DROP TABLE energy_service');
        $this->addSql('DROP TABLE energy_station');
        $this->addSql('DROP TABLE energy_station_brand');
        $this->addSql('DROP TABLE energy_type');
        $this->addSql('DROP TABLE google_place');
        $this->addSql('DROP TABLE user');
    }
}
