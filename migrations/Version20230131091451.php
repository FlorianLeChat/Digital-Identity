<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131091451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C97ECF78B0');
        $this->addSql('DROP INDEX UNIQ_765AE0C97ECF78B0 ON absence');
        $this->addSql('ALTER TABLE absence DROP cours_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence ADD cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C97ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_765AE0C97ECF78B0 ON absence (cours_id)');
    }
}
