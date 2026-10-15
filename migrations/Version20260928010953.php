<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260928010953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour_date ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE tour_date ADD CONSTRAINT FK_8BAED75AB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_8BAED75AB03A8386 ON tour_date (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour_date DROP CONSTRAINT FK_8BAED75AB03A8386');
        $this->addSql('DROP INDEX IDX_8BAED75AB03A8386');
        $this->addSql('ALTER TABLE tour_date DROP created_by_id');
    }
}
