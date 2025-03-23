<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221204905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_token (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_uuid VARCHAR(255) NOT NULL, access_token VARCHAR(1000) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_category (user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', category_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(user_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category CHANGE uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('ALTER TABLE category CHANGE uuid uuid VARCHAR(255) NOT NULL');
    }
}
