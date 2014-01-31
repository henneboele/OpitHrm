<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140131103552 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notes_notifications DROP FOREIGN KEY FK_6923A93F9246C527");
        $this->addSql("ALTER TABLE notes_notifications DROP FOREIGN KEY FK_6923A93F5F88AA01");
        $this->addSql("ALTER TABLE notes_notifications ADD CONSTRAINT FK_6923A93F9246C527 FOREIGN KEY (tr_id) REFERENCES notes_travel_request (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notes_notifications ADD CONSTRAINT FK_6923A93F5F88AA01 FOREIGN KEY (te_id) REFERENCES notes_travel_expense (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notes_notification_status CHANGE id id INT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notes_notification_status CHANGE id id INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE notes_notifications DROP FOREIGN KEY FK_6923A93F5F88AA01");
        $this->addSql("ALTER TABLE notes_notifications DROP FOREIGN KEY FK_6923A93F9246C527");
        $this->addSql("ALTER TABLE notes_notifications ADD CONSTRAINT FK_6923A93F5F88AA01 FOREIGN KEY (te_id) REFERENCES notes_travel_expense (id)");
        $this->addSql("ALTER TABLE notes_notifications ADD CONSTRAINT FK_6923A93F9246C527 FOREIGN KEY (tr_id) REFERENCES notes_travel_request (id)");
    }
}