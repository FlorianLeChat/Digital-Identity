<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118103052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_matiere (cours_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_D3123E317ECF78B0 (cours_id), INDEX IDX_D3123E31F46CD258 (matiere_id), PRIMARY KEY(cours_id, matiere_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_formation (cours_id INT NOT NULL, formation_id INT NOT NULL, INDEX IDX_B8E51B787ECF78B0 (cours_id), INDEX IDX_B8E51B785200282E (formation_id), PRIMARY KEY(cours_id, formation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_user (cours_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5EE5E9A67ECF78B0 (cours_id), INDEX IDX_5EE5E9A6A76ED395 (user_id), PRIMARY KEY(cours_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours_matiere ADD CONSTRAINT FK_D3123E317ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_matiere ADD CONSTRAINT FK_D3123E31F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_formation ADD CONSTRAINT FK_B8E51B787ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_formation ADD CONSTRAINT FK_B8E51B785200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_user ADD CONSTRAINT FK_5EE5E9A67ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_user ADD CONSTRAINT FK_5EE5E9A6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours_matiere DROP FOREIGN KEY FK_D3123E317ECF78B0');
        $this->addSql('ALTER TABLE cours_matiere DROP FOREIGN KEY FK_D3123E31F46CD258');
        $this->addSql('ALTER TABLE cours_formation DROP FOREIGN KEY FK_B8E51B787ECF78B0');
        $this->addSql('ALTER TABLE cours_formation DROP FOREIGN KEY FK_B8E51B785200282E');
        $this->addSql('ALTER TABLE cours_user DROP FOREIGN KEY FK_5EE5E9A67ECF78B0');
        $this->addSql('ALTER TABLE cours_user DROP FOREIGN KEY FK_5EE5E9A6A76ED395');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_matiere');
        $this->addSql('DROP TABLE cours_formation');
        $this->addSql('DROP TABLE cours_user');
    }
}
