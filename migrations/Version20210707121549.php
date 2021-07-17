<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707121549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE departure (id INT AUTO_INCREMENT NOT NULL, destination_id INT NOT NULL, reference VARCHAR(255) NOT NULL, date DATETIME NOT NULL, seat INT NOT NULL, rocket VARCHAR(255) NOT NULL, duration INT NOT NULL, price INT NOT NULL, INDEX IDX_45E9C671816C6140 (destination_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, short_description VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, picture_header VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE passenger (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, INDEX IDX_3BEFE8DDB83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_16DB4F8964D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, departure_id INT NOT NULL, returned_id INT NOT NULL, reference VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, status INT NOT NULL, stripe_reference VARCHAR(255) DEFAULT NULL, departure_price INT NOT NULL, return_price INT NOT NULL, INDEX IDX_42C8495519EB6921 (client_id), INDEX IDX_42C849557704ED06 (departure_id), INDEX IDX_42C849558667B3A4 (returned_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE returned (id INT AUTO_INCREMENT NOT NULL, ffrom_id INT NOT NULL, reference VARCHAR(255) NOT NULL, date DATETIME NOT NULL, seat INT NOT NULL, rocket VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL, price INT NOT NULL, INDEX IDX_2272BE3096047020 (ffrom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, password_reset_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birthday DATETIME NOT NULL, created_at DATETIME NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649637714BD (password_reset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE departure ADD CONSTRAINT FK_45E9C671816C6140 FOREIGN KEY (destination_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE passenger ADD CONSTRAINT FK_3BEFE8DDB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8964D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849557704ED06 FOREIGN KEY (departure_id) REFERENCES departure (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558667B3A4 FOREIGN KEY (returned_id) REFERENCES returned (id)');
        $this->addSql('ALTER TABLE returned ADD CONSTRAINT FK_2272BE3096047020 FOREIGN KEY (ffrom_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649637714BD FOREIGN KEY (password_reset_id) REFERENCES password_reset (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849557704ED06');
        $this->addSql('ALTER TABLE departure DROP FOREIGN KEY FK_45E9C671816C6140');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8964D218E');
        $this->addSql('ALTER TABLE returned DROP FOREIGN KEY FK_2272BE3096047020');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649637714BD');
        $this->addSql('ALTER TABLE passenger DROP FOREIGN KEY FK_3BEFE8DDB83297E7');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558667B3A4');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('DROP TABLE departure');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE passenger');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE returned');
        $this->addSql('DROP TABLE user');
    }
}
