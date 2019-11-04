<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104192042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE resident ADD house_id INT NOT NULL');
        $this->addSql('ALTER TABLE resident ADD CONSTRAINT FK_1D03DA066BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('CREATE INDEX IDX_1D03DA066BB74515 ON resident (house_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE resident DROP FOREIGN KEY FK_1D03DA066BB74515');
        $this->addSql('DROP INDEX IDX_1D03DA066BB74515 ON resident');
        $this->addSql('ALTER TABLE resident DROP house_id');
    }
}
