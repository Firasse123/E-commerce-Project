<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528101508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD user_id INT DEFAULT NULL, DROP user, DROP cart_items
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_BA388B7A76ED395 ON cart (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD cart_id INT NOT NULL, ADD product_id INT NOT NULL, DROP cart, DROP product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484454584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BEF484451AD5CDBF ON cart_items (cart_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BEF484454584665A ON cart_items (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP products
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD user_id INT NOT NULL, DROP user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD category_id INT NOT NULL, DROP category, DROP cart_items, CHANGE price price NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_BA388B7A76ED395 ON cart
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart ADD user VARCHAR(255) NOT NULL, ADD cart_items LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484451AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484454584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BEF484451AD5CDBF ON cart_items
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BEF484454584665A ON cart_items
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD cart VARCHAR(255) NOT NULL, ADD product VARCHAR(255) NOT NULL, DROP cart_id, DROP product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD products LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F5299398A76ED395 ON `order`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD user VARCHAR(255) NOT NULL, DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D34A04AD12469DE2 ON product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD category VARCHAR(255) NOT NULL, ADD cart_items LONGTEXT NOT NULL COMMENT '(DC2Type:array)', DROP category_id, CHANGE price price DOUBLE PRECISION NOT NULL
        SQL);
    }
}
