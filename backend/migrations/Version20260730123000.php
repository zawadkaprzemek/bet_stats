<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create bookmaker dictionary';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_bookmaker (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, emoji VARCHAR(32) DEFAULT NULL, sort_order INT NOT NULL, is_active TINYINT(1) NOT NULL, is_legal TINYINT(1) NOT NULL, default_tax_percent NUMERIC(5, 2) NOT NULL, website VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_28F4AF045E237E06 (name), UNIQUE INDEX UNIQ_28F4AF04989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE app_bookmaker');
    }
}
