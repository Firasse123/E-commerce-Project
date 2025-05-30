<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529162502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_52EA1F094584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD billing_first_name VARCHAR(100) DEFAULT NULL, ADD billing_last_name VARCHAR(100) DEFAULT NULL, ADD billing_address LONGTEXT DEFAULT NULL, ADD billing_zip_code VARCHAR(10) DEFAULT NULL, ADD billing_city VARCHAR(100) DEFAULT NULL, ADD billing_phone VARCHAR(20) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD notes LONGTEXT DEFAULT NULL, CHANGE delivery_zip_code delivery_zip_code VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD total_sold INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_item
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP billing_first_name, DROP billing_last_name, DROP billing_address, DROP billing_zip_code, DROP billing_city, DROP billing_phone, DROP phone, DROP notes, CHANGE delivery_zip_code delivery_zip_code VARCHAR(10) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP total_sold
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP is_verified
        SQL);
    }
}
