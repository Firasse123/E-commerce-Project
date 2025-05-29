<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528185616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD billing_first_name VARCHAR(100) DEFAULT NULL, ADD billing_last_name VARCHAR(100) DEFAULT NULL, ADD billing_address LONGTEXT DEFAULT NULL, ADD billing_zip_code VARCHAR(10) DEFAULT NULL, ADD billing_city VARCHAR(100) DEFAULT NULL, ADD billing_phone VARCHAR(20) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD notes LONGTEXT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP billing_first_name, DROP billing_last_name, DROP billing_address, DROP billing_zip_code, DROP billing_city, DROP billing_phone, DROP phone, DROP notes
        SQL);
    }
}
