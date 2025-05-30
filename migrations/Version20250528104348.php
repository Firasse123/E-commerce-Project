<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528104348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE category CHANGE name name VARCHAR(100) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD delivery_first_name VARCHAR(100) NOT NULL, ADD delivery_last_name VARCHAR(100) NOT NULL, ADD delivery_zip_code VARCHAR(10) NOT NULL, DROP delivery_firstname, DROP delivery_lastname, CHANGE reference reference VARCHAR(20) NOT NULL, CHANGE order_details order_details JSON NOT NULL COMMENT '(DC2Type:json)', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE delivery_address delivery_address LONGTEXT NOT NULL, CHANGE delivery_city delivery_city VARCHAR(100) NOT NULL, CHANGE delivery_phone delivery_phone VARCHAR(20) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_F5299398AEA34913 ON `order` (reference)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE description description LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE size size VARCHAR(10) DEFAULT NULL, CHANGE brand brand VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD first_name VARCHAR(100) NOT NULL, ADD last_name VARCHAR(100) NOT NULL, ADD address LONGTEXT DEFAULT NULL, ADD zip_code VARCHAR(10) DEFAULT NULL, DROP firstname, DROP lastname, DROP adress, CHANGE email email VARCHAR(180) NOT NULL, CHANGE roles roles JSON NOT NULL COMMENT '(DC2Type:json)', CHANGE city city VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE category CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_F5299398AEA34913 ON `order`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD delivery_firstname VARCHAR(100) NOT NULL, ADD delivery_lastname VARCHAR(100) NOT NULL, DROP delivery_first_name, DROP delivery_last_name, DROP delivery_zip_code, CHANGE reference reference VARCHAR(255) NOT NULL, CHANGE order_details order_details LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE delivery_address delivery_address VARCHAR(255) NOT NULL, CHANGE delivery_city delivery_city VARCHAR(40) NOT NULL, CHANGE delivery_phone delivery_phone VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP updated_at, CHANGE description description VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE size size VARCHAR(40) NOT NULL, CHANGE brand brand VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649E7927C74 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD adress VARCHAR(255) DEFAULT NULL, DROP first_name, DROP last_name, DROP address, DROP zip_code, CHANGE email email VARCHAR(255) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE city city VARCHAR(50) NOT NULL
        SQL);
    }
}
