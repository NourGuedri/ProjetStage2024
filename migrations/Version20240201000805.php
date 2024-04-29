<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201000805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leave_balance (id INT AUTO_INCREMENT NOT NULL, leave_type_id INT NOT NULL, total INT NOT NULL, remaining INT NOT NULL, consumed INT NOT NULL, UNIQUE INDEX UNIQ_EAAB67198313F474 (leave_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leave_type (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_leave (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, leave_type_id INT NOT NULL, start_date DATE NOT NULL, status VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, end_date DATE NOT NULL, INDEX IDX_C6A73CF5A76ED395 (user_id), INDEX IDX_C6A73CF58313F474 (leave_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leave_balance ADD CONSTRAINT FK_EAAB67198313F474 FOREIGN KEY (leave_type_id) REFERENCES leave_type (id)');
        $this->addSql('ALTER TABLE request_leave ADD CONSTRAINT FK_C6A73CF5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request_leave ADD CONSTRAINT FK_C6A73CF58313F474 FOREIGN KEY (leave_type_id) REFERENCES leave_type (id)');
        $this->addSql('ALTER TABLE leavetype DROP FOREIGN KEY leavetype_ibfk_1');
        $this->addSql('ALTER TABLE requestleave DROP FOREIGN KEY requestleave_ibfk_1');
        $this->addSql('ALTER TABLE requestleave DROP FOREIGN KEY requestleave_ibfk_2');
        $this->addSql('DROP TABLE leavebalance');
        $this->addSql('DROP TABLE leavetype');
        $this->addSql('DROP TABLE requestleave');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leavebalance (id INT AUTO_INCREMENT NOT NULL, total INT NOT NULL, remaining INT NOT NULL, consumed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE leavetype (id INT AUTO_INCREMENT NOT NULL, id_solde INT NOT NULL, description VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, name VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_solde (id_solde), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE requestleave (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_type INT NOT NULL, startDate DATE NOT NULL, status VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, reason VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, endDate DATE NOT NULL, INDEX id_user (id_user), INDEX id_type (id_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE leavetype ADD CONSTRAINT leavetype_ibfk_1 FOREIGN KEY (id_solde) REFERENCES leavebalance (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE requestleave ADD CONSTRAINT requestleave_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE requestleave ADD CONSTRAINT requestleave_ibfk_2 FOREIGN KEY (id_type) REFERENCES leavetype (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE leave_balance DROP FOREIGN KEY FK_EAAB67198313F474');
        $this->addSql('ALTER TABLE request_leave DROP FOREIGN KEY FK_C6A73CF5A76ED395');
        $this->addSql('ALTER TABLE request_leave DROP FOREIGN KEY FK_C6A73CF58313F474');
        $this->addSql('DROP TABLE leave_balance');
        $this->addSql('DROP TABLE leave_type');
        $this->addSql('DROP TABLE request_leave');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
