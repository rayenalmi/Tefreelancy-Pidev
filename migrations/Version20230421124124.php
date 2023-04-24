<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421124124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX workspace-post-freelancer ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX workspace_post_freelancer ON freelancer_post (freelancer_id, publication_id, workspace_id)');
        $this->addSql('DROP INDEX freelancer-post ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX freelancer_post ON freelancer_post (freelancer_id, publication_id)');
        $this->addSql('ALTER TABLE grouppostlikes ADD PRIMARY KEY (IdGroupPost, IdUser, idGroup)');
        $this->addSql('ALTER TABLE history CHANGE id_freelancer id_freelancer INT DEFAULT NULL, CHANGE rating rating DOUBLE PRECISION NOT NULL, CHANGE budget budget DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY offer_ibfk_1');
        $this->addSql('ALTER TABLE offer CHANGE id_recruter id_recruter INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873EA6E817D8 FOREIGN KEY (id_recruter) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_1');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_2');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY likes_ibfk_2');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DC753C60E FOREIGN KEY (id_offer) REFERENCES offer (id_offer)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('DROP INDEX id_user ON likes');
        $this->addSql('CREATE INDEX IDX_49CA4E7D6B3CA4B ON likes (id_user)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY project_ibfk_1');
        $this->addSql('DROP INDEX id_portfolio ON project');
        $this->addSql('ALTER TABLE project ADD id_freelancer INT DEFAULT NULL, DROP id_portfolio');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEE9A4513F FOREIGN KEY (id_freelancer) REFERENCES portfolio (id_portfolio)');
        $this->addSql('CREATE INDEX id_portfolio ON project (id_freelancer)');
        $this->addSql('ALTER TABLE publication_ws DROP likeCount');
        $this->addSql('ALTER TABLE skills DROP FOREIGN KEY fk_skills');
        $this->addSql('ALTER TABLE skills DROP FOREIGN KEY fk_skills');
        $this->addSql('ALTER TABLE skills ADD level VARCHAR(255) DEFAULT NULL, CHANGE id_freelancer id_freelancer INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skills ADD CONSTRAINT FK_D5311670E9A4513F FOREIGN KEY (id_freelancer) REFERENCES user (id_user)');
        $this->addSql('DROP INDEX fk_skills ON skills');
        $this->addSql('CREATE INDEX fk_skills_freelancer ON skills (id_freelancer)');
        $this->addSql('ALTER TABLE skills ADD CONSTRAINT fk_skills FOREIGN KEY (id_freelancer) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_formation DROP FOREIGN KEY user_formation_ibfk_1');
        $this->addSql('ALTER TABLE user_formation DROP FOREIGN KEY fsk');
        $this->addSql('ALTER TABLE user_formation ADD CONSTRAINT FK_40A0AC5B6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE users_community ADD PRIMARY KEY (id_community, id_user)');
        $this->addSql('ALTER TABLE workspace DROP idoffer');
        $this->addSql('DROP INDEX workspace-freelancer ON workspace_freelancer');
        $this->addSql('CREATE UNIQUE INDEX workspace_freelancer ON workspace_freelancer (workspace_id, freelancer_id)');
        $this->addSql('DROP INDEX workspace-post ON workspace_post');
        $this->addSql('CREATE UNIQUE INDEX workspace_post ON workspace_post (workspace_id, publication_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX workspace_post_freelancer ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX workspace-post-freelancer ON freelancer_post (freelancer_id, publication_id, workspace_id)');
        $this->addSql('DROP INDEX freelancer_post ON freelancer_post');
        $this->addSql('CREATE UNIQUE INDEX freelancer-post ON freelancer_post (freelancer_id, publication_id)');
        $this->addSql('DROP INDEX `primary` ON grouppostlikes');
        $this->addSql('ALTER TABLE history CHANGE id_freelancer id_freelancer INT NOT NULL, CHANGE rating rating INT NOT NULL, CHANGE budget budget INT NOT NULL');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DC753C60E');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D6B3CA4B');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D6B3CA4B');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_1 FOREIGN KEY (id_offer) REFERENCES offer (id_offer) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT likes_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_49ca4e7d6b3ca4b ON likes');
        $this->addSql('CREATE INDEX id_user ON likes (id_user)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873EA6E817D8');
        $this->addSql('ALTER TABLE offer CHANGE id_recruter id_recruter INT NOT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT offer_ibfk_1 FOREIGN KEY (id_recruter) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEE9A4513F');
        $this->addSql('DROP INDEX id_portfolio ON project');
        $this->addSql('ALTER TABLE project ADD id_portfolio INT NOT NULL, DROP id_freelancer');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT project_ibfk_1 FOREIGN KEY (id_portfolio) REFERENCES portfolio (id_portfolio)');
        $this->addSql('CREATE INDEX id_portfolio ON project (id_portfolio)');
        $this->addSql('ALTER TABLE publication_ws ADD likeCount INT NOT NULL');
        $this->addSql('ALTER TABLE skills DROP FOREIGN KEY FK_D5311670E9A4513F');
        $this->addSql('ALTER TABLE skills DROP FOREIGN KEY FK_D5311670E9A4513F');
        $this->addSql('ALTER TABLE skills DROP level, CHANGE id_freelancer id_freelancer INT NOT NULL');
        $this->addSql('ALTER TABLE skills ADD CONSTRAINT fk_skills FOREIGN KEY (id_freelancer) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX fk_skills_freelancer ON skills');
        $this->addSql('CREATE INDEX fk_skills ON skills (id_freelancer)');
        $this->addSql('ALTER TABLE skills ADD CONSTRAINT FK_D5311670E9A4513F FOREIGN KEY (id_freelancer) REFERENCES user (id_user)');
        $this->addSql('DROP INDEX `primary` ON users_community');
        $this->addSql('ALTER TABLE user_formation DROP FOREIGN KEY FK_40A0AC5B6B3CA4B');
        $this->addSql('ALTER TABLE user_formation ADD CONSTRAINT user_formation_ibfk_1 FOREIGN KEY (id_formation) REFERENCES formation (id_formation) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_formation ADD CONSTRAINT fsk FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workspace ADD idoffer INT NOT NULL');
        $this->addSql('DROP INDEX workspace_freelancer ON workspace_freelancer');
        $this->addSql('CREATE UNIQUE INDEX workspace-freelancer ON workspace_freelancer (workspace_id, freelancer_id)');
        $this->addSql('DROP INDEX workspace_post ON workspace_post');
        $this->addSql('CREATE UNIQUE INDEX workspace-post ON workspace_post (workspace_id, publication_id)');
    }
}
