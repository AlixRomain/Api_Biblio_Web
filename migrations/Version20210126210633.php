<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126210633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livre_caduc (id INT AUTO_INCREMENT NOT NULL, genre_id INT NOT NULL, editeur_id INT NOT NULL, auteur_id INT NOT NULL, isbn VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION DEFAULT NULL, annee INT DEFAULT NULL, langue VARCHAR(255) DEFAULT NULL, INDEX IDX_C1042C2D4296D31F (genre_id), INDEX IDX_C1042C2D3375BD21 (editeur_id), INDEX IDX_C1042C2D60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livre_caduc ADD CONSTRAINT FK_C1042C2D4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE livre_caduc ADD CONSTRAINT FK_C1042C2D3375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id)');
        $this->addSql('ALTER TABLE livre_caduc ADD CONSTRAINT FK_C1042C2D60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id)');
        $this->addSql('ALTER TABLE adherent CHANGE mail mail VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE livre_caduc');
        $this->addSql('ALTER TABLE adherent CHANGE mail mail VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
