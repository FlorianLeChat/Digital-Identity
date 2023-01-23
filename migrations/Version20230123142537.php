<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123142537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE presence_cours (presence_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_D4F8BC1CF328FFC4 (presence_id), INDEX IDX_D4F8BC1C7ECF78B0 (cours_id), PRIMARY KEY(presence_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE presence_user (presence_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_666ACE30F328FFC4 (presence_id), INDEX IDX_666ACE30A76ED395 (user_id), PRIMARY KEY(presence_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE presence_cours ADD CONSTRAINT FK_D4F8BC1CF328FFC4 FOREIGN KEY (presence_id) REFERENCES presence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE presence_cours ADD CONSTRAINT FK_D4F8BC1C7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE presence_user ADD CONSTRAINT FK_666ACE30F328FFC4 FOREIGN KEY (presence_id) REFERENCES presence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE presence_user ADD CONSTRAINT FK_666ACE30A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE presence_cours DROP FOREIGN KEY FK_D4F8BC1CF328FFC4');
        $this->addSql('ALTER TABLE presence_cours DROP FOREIGN KEY FK_D4F8BC1C7ECF78B0');
        $this->addSql('ALTER TABLE presence_user DROP FOREIGN KEY FK_666ACE30F328FFC4');
        $this->addSql('ALTER TABLE presence_user DROP FOREIGN KEY FK_666ACE30A76ED395');
        $this->addSql('DROP TABLE presence_cours');
        $this->addSql('DROP TABLE presence_user');
    }
}
