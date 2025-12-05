<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251205081544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_task (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, phase VARCHAR(50) DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, document_link VARCHAR(500) DEFAULT NULL, project_id INT NOT NULL, INDEX IDX_6BEF133D166D1F9C (project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_identifier_firstname ON user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64983A00E68 ON user (firstname)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_task DROP FOREIGN KEY FK_6BEF133D166D1F9C');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_task');
        $this->addSql('ALTER TABLE `user` CHANGE lastname lastname VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX uniq_8d93d64983a00e68 ON `user`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_FIRSTNAME ON `user` (firstname)');
    }
}
