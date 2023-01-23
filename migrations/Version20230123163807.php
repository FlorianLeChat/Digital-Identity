<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123163807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, justification_statut TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_765AE0C97ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE absence_user (absence_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FA8218D62DFF238F (absence_id), INDEX IDX_FA8218D6A76ED395 (user_id), PRIMARY KEY(absence_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C97ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE absence_user ADD CONSTRAINT FK_FA8218D62DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE absence_user ADD CONSTRAINT FK_FA8218D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C97ECF78B0');
        $this->addSql('ALTER TABLE absence_user DROP FOREIGN KEY FK_FA8218D62DFF238F');
        $this->addSql('ALTER TABLE absence_user DROP FOREIGN KEY FK_FA8218D6A76ED395');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE absence_user');
    }
}
