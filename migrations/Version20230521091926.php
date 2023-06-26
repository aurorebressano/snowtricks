<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521091926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture ADD header TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE trick CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91e9d86650f TO IDX_D8F0A91EA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP header');
        $this->addSql('ALTER TABLE trick CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91ea76ed395 TO IDX_D8F0A91E9D86650F');
    }
}
