<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230115184430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matiere_user (matiere_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FE415017F46CD258 (matiere_id), INDEX IDX_FE415017A76ED395 (user_id), PRIMARY KEY(matiere_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE matiere_user ADD CONSTRAINT FK_FE415017F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matiere_user ADD CONSTRAINT FK_FE415017A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matiere_user DROP FOREIGN KEY FK_FE415017F46CD258');
        $this->addSql('ALTER TABLE matiere_user DROP FOREIGN KEY FK_FE415017A76ED395');
        $this->addSql('DROP TABLE matiere_user');
    }
}
