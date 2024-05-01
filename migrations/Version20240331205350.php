<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331205350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE series CHANGE liked liked INT DEFAULT NULL, CHANGE nbLikes nbLikes INT DEFAULT NULL, CHANGE disliked disliked INT DEFAULT NULL, CHANGE nbDislikes nbDislikes INT DEFAULT NULL, CHANGE idcategorie idcategorie INT DEFAULT NULL');
        $this->addSql('ALTER TABLE series ADD CONSTRAINT FK_3A10012D37667FC1 FOREIGN KEY (idcategorie) REFERENCES categories (idcategorie)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE series DROP FOREIGN KEY FK_3A10012D37667FC1');
        $this->addSql('ALTER TABLE series CHANGE idcategorie idcategorie INT NOT NULL, CHANGE liked liked INT NOT NULL, CHANGE nbLikes nbLikes INT NOT NULL, CHANGE disliked disliked INT NOT NULL, CHANGE nbDislikes nbDislikes INT NOT NULL');
    }
}
