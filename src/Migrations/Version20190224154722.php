<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190224154722 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // Forgotten in previous migrations
        $this->addSql('DELETE FROM app_user_year_stats WHERE year = 2021');

        $this->addSql('CREATE TABLE supported_holiday (day INT NOT NULL, month INT NOT NULL, year INT NOT NULL, PRIMARY KEY(day, month, year))');
        $this->addSql('CREATE INDEX IDX_E375C161BB827337 ON supported_holiday (year)');
        $this->addSql('CREATE TABLE supported_year (year INT NOT NULL, PRIMARY KEY(year))');
        $this->addSql('ALTER TABLE supported_holiday ADD CONSTRAINT FK_E375C161BB827337 FOREIGN KEY (year) REFERENCES supported_year (year) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Copy values from existing config
        $this->addSql('
          INSERT INTO supported_year
          (year) VALUES
          (2018),
          (2019),
          (2020),
          (2021),
          (2022),
          (2023),
          (2024)'
        );
        $this->addSql('
          INSERT INTO supported_holiday
          (day, month, year) VALUES
            (1, 1, 2018),
            (30, 3, 2018),
            (2, 4, 2018),
            (1, 5, 2018),
            (10, 5, 2018),
            (11, 5, 2018),
            (21, 5, 2018),
            (3, 10, 2018),
            (24, 12, 2018),
            (25, 12, 2018),
            (26, 12, 2018),
            (31, 12, 2018),
            (1, 1, 2019),
            (8, 3, 2019),
            (19, 4, 2019),
            (22, 4, 2019),
            (1, 5, 2019),
            (30, 5, 2019),
            (31, 5, 2019),
            (10, 6, 2019),
            (3, 10, 2019),
            (24, 12, 2019),
            (25, 12, 2019),
            (26, 12, 2019),
            (31, 12, 2019),
            (1, 1, 2020),
            (8, 3, 2020),
            (10, 4, 2020),
            (13, 4, 2020),
            (1, 5, 2020),
            (21, 5, 2020),
            (22, 5, 2020),
            (1, 6, 2020),
            (3, 10, 2020),
            (24, 12, 2020),
            (25, 12, 2020),
            (26, 12, 2020),
            (31, 12, 2020),
            (1, 1, 2021),
            (8, 3, 2021),
            (10, 4, 2021),
            (13, 4, 2021),
            (1, 5, 2021),
            (21, 5, 2021),
            (22, 5, 2021),
            (1, 6, 2021),
            (3, 10, 2021),
            (24, 12, 2021),
            (25, 12, 2021),
            (26, 12, 2021),
            (31, 12, 2021),
            (1, 1, 2022),
            (8, 3, 2022),
            (10, 4, 2022),
            (13, 4, 2022),
            (1, 5, 2022),
            (21, 5, 2022),
            (22, 5, 2022),
            (1, 6, 2022),
            (3, 10, 2022),
            (24, 12, 2022),
            (25, 12, 2022),
            (26, 12, 2022),
            (31, 12, 2022),
            (1, 1, 2023),
            (8, 3, 2023),
            (10, 4, 2023),
            (13, 4, 2023),
            (1, 5, 2023),
            (21, 5, 2023),
            (22, 5, 2023),
            (1, 6, 2023),
            (3, 10, 2023),
            (24, 12, 2023),
            (25, 12, 2023),
            (26, 12, 2023),
            (31, 12, 2023),
            (1, 1, 2024),
            (8, 3, 2024),
            (10, 4, 2024),
            (13, 4, 2024),
            (1, 5, 2024),
            (21, 5, 2024),
            (22, 5, 2024),
            (1, 6, 2024),
            (3, 10, 2024),
            (24, 12, 2024),
            (25, 12, 2024),
            (26, 12, 2024),
            (31, 12, 2024)
        ');

        $this->addSql('ALTER TABLE app_user_year_stats ADD CONSTRAINT FK_994AA18BB827337 FOREIGN KEY (year) REFERENCES supported_year (year) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_hours ADD CONSTRAINT FK_A2E1C6A2BB827337 FOREIGN KEY (year) REFERENCES supported_year (year) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A2E1C6A2BB827337 ON work_hours (year)');
        $this->addSql('ALTER TABLE work_hours DROP CONSTRAINT work_hours_pkey');
        $this->addSql('ALTER TABLE work_hours ADD PRIMARY KEY (month, user_id, year)');
        $this->addSql('ALTER TABLE work_month ADD CONSTRAINT FK_A64D6B29BB827337 FOREIGN KEY (year) REFERENCES supported_year (year) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A64D6B29BB827337 ON work_month (year)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE app_user_year_stats DROP CONSTRAINT FK_994AA18BB827337');
        $this->addSql('ALTER TABLE supported_holiday DROP CONSTRAINT FK_E375C161BB827337');
        $this->addSql('ALTER TABLE work_hours DROP CONSTRAINT FK_A2E1C6A2BB827337');
        $this->addSql('ALTER TABLE work_month DROP CONSTRAINT FK_A64D6B29BB827337');
        $this->addSql('DROP TABLE supported_holiday');
        $this->addSql('DROP TABLE supported_year');
        $this->addSql('DROP INDEX IDX_A2E1C6A2BB827337');
        $this->addSql('DROP INDEX work_hours_pkey');
        $this->addSql('ALTER TABLE work_hours ADD PRIMARY KEY (month, year, user_id)');
        $this->addSql('DROP INDEX IDX_A64D6B29BB827337');
    }
}
