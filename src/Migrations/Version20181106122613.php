<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181106122613 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE home_office_work_log ADD comment TEXT NULL');
        $this->addSql('ALTER TABLE time_off_work_log ADD comment TEXT NULL');
        $this->addSql('UPDATE home_office_work_log SET comment = \'\'');
        $this->addSql('UPDATE time_off_work_log SET comment = \'\'');
        $this->addSql('ALTER TABLE home_office_work_log ALTER COLUMN comment SET NOT NULL');
        $this->addSql('ALTER TABLE time_off_work_log ALTER COLUMN comment SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE time_off_work_log DROP comment');
        $this->addSql('ALTER TABLE home_office_work_log DROP comment');
    }
}
