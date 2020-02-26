<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226205634 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_users ALTER surname DROP NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER postcode DROP NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER city DROP NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER street DROP NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER country DROP NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER phone DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE app_users ALTER surname SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER postcode SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER city SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER street SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER country SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER phone SET NOT NULL');
    }
}
