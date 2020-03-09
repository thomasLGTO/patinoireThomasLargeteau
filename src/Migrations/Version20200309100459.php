<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200309100459 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tips (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title_tips VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, keywords VARCHAR(255) NOT NULL, content_tips VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, refusal_reason VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, number_users INT DEFAULT NULL, INDEX IDX_642C410812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tips_user (tips_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F3A8C3ECB3E8864B (tips_id), INDEX IDX_F3A8C3ECA76ED395 (user_id), PRIMARY KEY(tips_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tips ADD CONSTRAINT FK_642C410812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE tips_user ADD CONSTRAINT FK_F3A8C3ECB3E8864B FOREIGN KEY (tips_id) REFERENCES tips (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tips_user ADD CONSTRAINT FK_F3A8C3ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tips_user DROP FOREIGN KEY FK_F3A8C3ECB3E8864B');
        $this->addSql('DROP TABLE tips');
        $this->addSql('DROP TABLE tips_user');
    }
}
