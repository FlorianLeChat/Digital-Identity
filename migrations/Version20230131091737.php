<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131091737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence_cours (absence_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_9D0D13872DFF238F (absence_id), INDEX IDX_9D0D13877ECF78B0 (cours_id), PRIMARY KEY(absence_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence_cours ADD CONSTRAINT FK_9D0D13872DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE absence_cours ADD CONSTRAINT FK_9D0D13877ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence_cours DROP FOREIGN KEY FK_9D0D13872DFF238F');
        $this->addSql('ALTER TABLE absence_cours DROP FOREIGN KEY FK_9D0D13877ECF78B0');
        $this->addSql('DROP TABLE absence_cours');
    }
}
