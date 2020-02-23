<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200223230608 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE clan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE clan (id INT NOT NULL, name VARCHAR(255) NOT NULL, owner_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "user" ADD clan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD nickname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD status INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD firstname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD surname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD postcode INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD street VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD country VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD gender VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD email_confirmed BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649BEAF84C8 FOREIGN KEY (clan_id) REFERENCES clan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649BEAF84C8 ON "user" (clan_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649BEAF84C8');
        $this->addSql('DROP SEQUENCE clan_id_seq CASCADE');
        $this->addSql('DROP TABLE clan');
        $this->addSql('DROP INDEX IDX_8D93D649BEAF84C8');
        $this->addSql('ALTER TABLE "user" DROP clan_id');
        $this->addSql('ALTER TABLE "user" DROP nickname');
        $this->addSql('ALTER TABLE "user" DROP email');
        $this->addSql('ALTER TABLE "user" DROP password');
        $this->addSql('ALTER TABLE "user" DROP status');
        $this->addSql('ALTER TABLE "user" DROP firstname');
        $this->addSql('ALTER TABLE "user" DROP surname');
        $this->addSql('ALTER TABLE "user" DROP postcode');
        $this->addSql('ALTER TABLE "user" DROP city');
        $this->addSql('ALTER TABLE "user" DROP street');
        $this->addSql('ALTER TABLE "user" DROP country');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" DROP gender');
        $this->addSql('ALTER TABLE "user" DROP email_confirmed');
    }
}
