<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create region dictionary, relate competitions to regions, and rename sport icon to emoji';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, emoji VARCHAR(32) DEFAULT NULL, sort_order INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_58A2A7D45E237E06 (name), UNIQUE INDEX UNIQ_58A2A7D4989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_sport CHANGE icon emoji VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_competition ADD region_id INT DEFAULT NULL, DROP country_code');
        $this->addSql('ALTER TABLE app_competition ADD CONSTRAINT FK_2269F3F598260155 FOREIGN KEY (region_id) REFERENCES app_region (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2269F3F598260155 ON app_competition (region_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_competition DROP FOREIGN KEY FK_2269F3F598260155');
        $this->addSql('DROP TABLE app_region');
        $this->addSql('DROP INDEX IDX_2269F3F598260155 ON app_competition');
        $this->addSql('ALTER TABLE app_competition ADD country_code VARCHAR(2) DEFAULT NULL, DROP region_id');
        $this->addSql('ALTER TABLE app_sport CHANGE emoji icon VARCHAR(120) NOT NULL');
    }
}
