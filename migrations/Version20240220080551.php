<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220080551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_station ADD ev_information_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE energy_station ADD CONSTRAINT FK_DA071265E709FFAB FOREIGN KEY (ev_information_id) REFERENCES ev_information (id)');
        $this->addSql('CREATE INDEX IDX_DA071265E709FFAB ON energy_station (ev_information_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy_station DROP FOREIGN KEY FK_DA071265E709FFAB');
        $this->addSql('DROP INDEX IDX_DA071265E709FFAB ON energy_station');
        $this->addSql('ALTER TABLE energy_station DROP ev_information_id');
    }
}
