<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113232347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_d34a04ad32e6b28b ON product');
        $this->addSql('CREATE UNIQUE INDEX str_product_code ON product (str_product_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX str_product_code ON product');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD32E6B28B ON product (str_product_code)');
    }
}
