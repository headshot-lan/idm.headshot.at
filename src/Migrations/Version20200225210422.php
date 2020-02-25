<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225210422 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE api_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE api_user (id INT NOT NULL, name VARCHAR(180) NOT NULL, roles JSON NOT NULL, api_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC64A0BA5E237E06 ON api_user (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC64A0BA7BA2F5EB ON api_user (api_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824EBD1F0C ON app_users (extern_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE api_user_id_seq CASCADE');
        $this->addSql('DROP TABLE api_user');
        $this->addSql('DROP INDEX UNIQ_C2502824EBD1F0C');
    }
}
