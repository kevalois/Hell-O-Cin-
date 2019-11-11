<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190925124019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE person ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE movie ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE casting ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8AE80F5DF');
        $this->addSql('ALTER TABLE job ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE department_id department_id INT NOT NULL, CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE department ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE name name VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE casting DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE department DROP created_at, DROP updated_at, CHANGE name name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE genre DROP created_at, DROP updated_at, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8AE80F5DF');
        $this->addSql('ALTER TABLE job DROP created_at, DROP updated_at, CHANGE department_id department_id INT DEFAULT NULL, CHANGE name name VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE movie DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE person DROP created_at, DROP updated_at, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
