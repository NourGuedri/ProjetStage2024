<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206014822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_leave (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, leave_type_id INT NOT NULL, start_date DATE NOT NULL, status VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, end_date DATE NOT NULL, INDEX IDX_C6A73CF5A76ED395 (user_id), INDEX IDX_C6A73CF58313F474 (leave_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE request_leave ADD CONSTRAINT FK_C6A73CF5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request_leave ADD CONSTRAINT FK_C6A73CF58313F474 FOREIGN KEY (leave_type_id) REFERENCES leave_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request_leave DROP FOREIGN KEY FK_C6A73CF5A76ED395');
        $this->addSql('ALTER TABLE request_leave DROP FOREIGN KEY FK_C6A73CF58313F474');
        $this->addSql('DROP TABLE request_leave');
    }
}
