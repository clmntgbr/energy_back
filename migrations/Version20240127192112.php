<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127192112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_price ADD energy_station_id INT NOT NULL, ADD energy_type_id INT NOT NULL, ADD currency_id INT NOT NULL, ADD value INT NOT NULL, ADD date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD date_timestamp INT NOT NULL, ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F11792CECD2951 FOREIGN KEY (energy_station_id) REFERENCES energy_station (id)');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F1179280726647 FOREIGN KEY (energy_type_id) REFERENCES energy_type (id)');
        $this->addSql('ALTER TABLE energy_price ADD CONSTRAINT FK_72F1179238248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72F11792D17F50A6 ON energy_price (uuid)');
        $this->addSql('CREATE INDEX IDX_72F11792CECD2951 ON energy_price (energy_station_id)');
        $this->addSql('CREATE INDEX IDX_72F1179280726647 ON energy_price (energy_type_id)');
        $this->addSql('CREATE INDEX IDX_72F1179238248176 ON energy_price (currency_id)');
        $this->addSql('ALTER TABLE energy_service ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD name VARCHAR(255) NOT NULL, ADD reference VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A4A37006D17F50A6 ON energy_service (uuid)');
        $this->addSql('ALTER TABLE energy_station_brand ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD name VARCHAR(255) NOT NULL, ADD reference VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_original_name VARCHAR(255) DEFAULT NULL, ADD image_mime_type VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD image_low_name VARCHAR(255) DEFAULT NULL, ADD image_low_original_name VARCHAR(255) DEFAULT NULL, ADD image_low_mime_type VARCHAR(255) DEFAULT NULL, ADD image_low_size INT DEFAULT NULL, ADD image_low_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9BAC790D17F50A6 ON energy_station_brand (uuid)');
        $this->addSql('ALTER TABLE energy_type ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD name VARCHAR(255) NOT NULL, ADD reference VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_original_name VARCHAR(255) DEFAULT NULL, ADD image_mime_type VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_84E131ED17F50A6 ON energy_type (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F11792CECD2951');
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F1179280726647');
        $this->addSql('ALTER TABLE energy_price DROP FOREIGN KEY FK_72F1179238248176');
        $this->addSql('DROP INDEX UNIQ_72F11792D17F50A6 ON energy_price');
        $this->addSql('DROP INDEX IDX_72F11792CECD2951 ON energy_price');
        $this->addSql('DROP INDEX IDX_72F1179280726647 ON energy_price');
        $this->addSql('DROP INDEX IDX_72F1179238248176 ON energy_price');
        $this->addSql('ALTER TABLE energy_price DROP energy_station_id, DROP energy_type_id, DROP currency_id, DROP value, DROP date, DROP date_timestamp, DROP uuid, DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('DROP INDEX UNIQ_A4A37006D17F50A6 ON energy_service');
        $this->addSql('ALTER TABLE energy_service DROP uuid, DROP name, DROP reference, DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('DROP INDEX UNIQ_C9BAC790D17F50A6 ON energy_station_brand');
        $this->addSql('ALTER TABLE energy_station_brand DROP uuid, DROP name, DROP reference, DROP created_at, DROP updated_at, DROP created_by, DROP updated_by, DROP image_name, DROP image_original_name, DROP image_mime_type, DROP image_size, DROP image_dimensions, DROP image_low_name, DROP image_low_original_name, DROP image_low_mime_type, DROP image_low_size, DROP image_low_dimensions');
        $this->addSql('DROP INDEX UNIQ_84E131ED17F50A6 ON energy_type');
        $this->addSql('ALTER TABLE energy_type DROP uuid, DROP name, DROP reference, DROP created_at, DROP updated_at, DROP created_by, DROP updated_by, DROP image_name, DROP image_original_name, DROP image_mime_type, DROP image_size, DROP image_dimensions');
    }
}
