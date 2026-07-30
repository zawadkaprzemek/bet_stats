<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tag system';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_tag (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, emoji VARCHAR(32) DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, is_system TINYINT(1) NOT NULL, sort_order INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4D58A8C07E3C61F9 (owner_id), INDEX idx_tag_system_sort (is_system, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_tag ADD CONSTRAINT FK_4D58A8C07E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_tag DROP FOREIGN KEY FK_4D58A8C07E3C61F9');
        $this->addSql('DROP TABLE app_tag');
    }
}
