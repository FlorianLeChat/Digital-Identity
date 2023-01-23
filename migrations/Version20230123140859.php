<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123140859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE termine terminé TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A5E0F315C4');
        $this->addSql('DROP INDEX IDX_6977C7A5E0F315C4 ON presence');
        $this->addSql('ALTER TABLE presence DROP stud_presence_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE terminé termine TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE presence ADD stud_presence_id INT NOT NULL');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A5E0F315C4 FOREIGN KEY (stud_presence_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6977C7A5E0F315C4 ON presence (stud_presence_id)');
    }
}
