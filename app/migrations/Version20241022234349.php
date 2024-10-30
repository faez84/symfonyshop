<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022234349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, zip VARCHAR(15) NOT NULL, defualt TINYINT(1) NOT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_product DROP INDEX UNIQ_2530ADE68D6EE88A, ADD INDEX IDX_2530ADE68D6EE88A (oorder_id)');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('DROP INDEX UNIQ_2530ADE64584665A ON order_product');
        $this->addSql('ALTER TABLE order_product CHANGE product_id pproduct_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE633ABED33 FOREIGN KEY (pproduct_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2530ADE633ABED33 ON order_product (pproduct_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('ALTER TABLE order_product DROP INDEX IDX_2530ADE68D6EE88A, ADD UNIQUE INDEX UNIQ_2530ADE68D6EE88A (oorder_id)');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE633ABED33');
        $this->addSql('DROP INDEX IDX_2530ADE633ABED33 ON order_product');
        $this->addSql('ALTER TABLE order_product CHANGE pproduct_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2530ADE64584665A ON order_product (product_id)');
    }
}
