<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222145103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ev_recharge_point ADD evinformation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ev_recharge_point ADD CONSTRAINT FK_DACB90AB744E5D5C FOREIGN KEY (evinformation_id) REFERENCES ev_information (id)');
        $this->addSql('CREATE INDEX IDX_DACB90AB744E5D5C ON ev_recharge_point (evinformation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ev_recharge_point DROP FOREIGN KEY FK_DACB90AB744E5D5C');
        $this->addSql('DROP INDEX IDX_DACB90AB744E5D5C ON ev_recharge_point');
        $this->addSql('ALTER TABLE ev_recharge_point DROP evinformation_id');
    }
}
