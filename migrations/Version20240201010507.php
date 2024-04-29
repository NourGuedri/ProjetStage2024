<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201010507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leave_balance DROP INDEX UNIQ_EAAB67198313F474, ADD INDEX IDX_EAAB67198313F474 (leave_type_id)');
        $this->addSql('ALTER TABLE leave_balance MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON leave_balance');
        $this->addSql('ALTER TABLE leave_balance ADD user_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE leave_balance ADD CONSTRAINT FK_EAAB6719A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EAAB6719A76ED395 ON leave_balance (user_id)');
        $this->addSql('ALTER TABLE leave_balance ADD PRIMARY KEY (user_id, leave_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leave_balance DROP INDEX IDX_EAAB67198313F474, ADD UNIQUE INDEX UNIQ_EAAB67198313F474 (leave_type_id)');
        $this->addSql('ALTER TABLE leave_balance DROP FOREIGN KEY FK_EAAB6719A76ED395');
        $this->addSql('DROP INDEX IDX_EAAB6719A76ED395 ON leave_balance');
        $this->addSql('ALTER TABLE leave_balance ADD id INT AUTO_INCREMENT NOT NULL, DROP user_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
