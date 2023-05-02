<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230415221930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge CHANGE id_test id_test INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidacy CHANGE id_offer id_offer INT DEFAULT NULL, CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569DE9A4513F FOREIGN KEY (id_freelancer) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569DC753C60E FOREIGN KEY (id_offer) REFERENCES offer (id_offer)');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY chapters_ibfk_1');
        $this->addSql('ALTER TABLE chapters CHANGE formation_id formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT FK_C72143715200282E FOREIGN KEY (formation_id) REFERENCES formation (id_formation)');
        $this->addSql('DROP INDEX workspace-post-freelancer ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX workspace_post_freelancer ON freelancer_post (freelancer_id, publication_id, workspace_id)');
        $this->addSql('DROP INDEX freelancer-post ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX freelancer_post ON freelancer_post (freelancer_id, publication_id)');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY history_ibfk_1');
        $this->addSql('ALTER TABLE history CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BE9A4513F FOREIGN KEY (id_freelancer) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY offer_ibfk_1');
        $this->addSql('ALTER TABLE offer CHANGE id_recruter id_recruter INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873EA6E817D8 FOREIGN KEY (id_recruter) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_2');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_1');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_2');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DC753C60E FOREIGN KEY (id_offer) REFERENCES offer (id_offer)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('DROP INDEX id_user ON likes');
        $this->addSql('CREATE INDEX IDX_49CA4E7D6B3CA4B ON likes (id_user)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skills CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_formation DROP FOREIGN KEY fsk');
        $this->addSql('ALTER TABLE user_formation ADD CONSTRAINT FK_40A0AC5B6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE user_freelancer_project CHANGE id_portfolio id_portfolio INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user_freelancer_skill CHANGE id_portfolio id_portfolio INT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP INDEX workspace-freelancer ON workspace_freelancer');
        $this->addSql('CREATE UNIQUE INDEX workspace_freelancer ON workspace_freelancer (workspace_id, freelancer_id)');
        $this->addSql('DROP INDEX workspace-post ON workspace_post');
        $this->addSql('CREATE UNIQUE INDEX workspace_post ON workspace_post (workspace_id, publication_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge CHANGE id_test id_test INT NOT NULL');
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569DE9A4513F');
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569DC753C60E');
        $this->addSql('ALTER TABLE candidacy CHANGE id_freelancer id_freelancer INT NOT NULL, CHANGE id_offer id_offer INT NOT NULL');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT candidacy_ibfk_1 FOREIGN KEY (id_offer) REFERENCES offer (id_offer) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT id_freelancer FOREIGN KEY (id_freelancer) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C72143715200282E');
        $this->addSql('ALTER TABLE chapters CHANGE formation_id formation_id INT NOT NULL');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT chapters_ibfk_1 FOREIGN KEY (formation_id) REFERENCES formation (id_formation) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX freelancer_post ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX freelancer-post ON freelancer_post (freelancer_id, publication_id)');
        $this->addSql('DROP INDEX workspace_post_freelancer ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX workspace-post-freelancer ON freelancer_post (freelancer_id, publication_id, workspace_id)');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BE9A4513F');
        $this->addSql('ALTER TABLE history CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT history_ibfk_1 FOREIGN KEY (id_freelancer) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DC753C60E');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D6B3CA4B');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D6B3CA4B');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_1 FOREIGN KEY (id_offer) REFERENCES offer (id_offer) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_49ca4e7d6b3ca4b ON likes');
        $this->addSql('CREATE INDEX id_user ON likes (id_user)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873EA6E817D8');
        $this->addSql('ALTER TABLE offer CHANGE id_recruter id_recruter INT NOT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT offer_ibfk_1 FOREIGN KEY (id_recruter) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE skills CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE user_formation DROP FOREIGN KEY FK_40A0AC5B6B3CA4B');
        $this->addSql('ALTER TABLE user_formation ADD CONSTRAINT fsk FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_freelancer_project CHANGE id_portfolio id_portfolio INT NOT NULL');
        $this->addSql('ALTER TABLE user_freelancer_skill CHANGE id_portfolio id_portfolio INT NOT NULL');
        $this->addSql('DROP INDEX workspace_freelancer ON workspace_freelancer');
        $this->addSql('CREATE UNIQUE INDEX workspace-freelancer ON workspace_freelancer (workspace_id, freelancer_id)');
        $this->addSql('DROP INDEX workspace_post ON workspace_post');
        $this->addSql('CREATE UNIQUE INDEX workspace-post ON workspace_post (workspace_id, publication_id)');
    }
}
