<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221211709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_category MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON user_category');
        $this->addSql('ALTER TABLE user_category DROP id, DROP uuid');
        $this->addSql('ALTER TABLE user_category ADD PRIMARY KEY (user_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_category ADD id INT AUTO_INCREMENT NOT NULL, ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
