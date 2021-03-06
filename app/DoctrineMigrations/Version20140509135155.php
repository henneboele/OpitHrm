<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140509135155 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE opithrm_leave_request (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, leave_request_id VARCHAR(11) DEFAULT NULL, INDEX IDX_74EBEE948C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE opithrm_leaves (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, description LONGTEXT DEFAULT NULL, leaveRequest_id INT DEFAULT NULL, INDEX IDX_4B0AF95F12469DE2 (category_id), INDEX IDX_4B0AF95FF5EC012 (leaveRequest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE opithrm_leave_request ADD CONSTRAINT FK_74EBEE948C03F15C FOREIGN KEY (employee_id) REFERENCES opithrm_employees (id)");
        $this->addSql("ALTER TABLE opithrm_leaves ADD CONSTRAINT FK_4B0AF95F12469DE2 FOREIGN KEY (category_id) REFERENCES opithrm_holiday_categories (id)");
        $this->addSql("ALTER TABLE opithrm_leaves ADD CONSTRAINT FK_4B0AF95FF5EC012 FOREIGN KEY (leaveRequest_id) REFERENCES opithrm_leave_request (id)");

        // Get old data
        $employees = $this->connection->fetchAll("SELECT id, dateOfBirth, joiningDate FROM opithrm_employees");

        $this->addSql("ALTER TABLE opithrm_employees ADD deletedAt DATETIME DEFAULT NULL, ADD date_of_birth DATE NOT NULL, ADD joining_date DATE NOT NULL, DROP dateOfBirth, DROP joiningDate, CHANGE numberofchildren number_of_children INT NOT NULL");
        $this->addSql("ALTER TABLE opithrm_users DROP FOREIGN KEY FK_8E744D495D9F75A1");
        $this->addSql("DROP INDEX UNIQ_8E744D495D9F75A1 ON opithrm_users");
        $this->addSql("ALTER TABLE opithrm_users CHANGE employee employee_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE opithrm_users ADD CONSTRAINT FK_8E744D498C03F15C FOREIGN KEY (employee_id) REFERENCES opithrm_employees (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_8E744D498C03F15C ON opithrm_users (employee_id)");
        $this->addSql("ALTER TABLE opithrm_holiday_categories ADD deletedAt DATETIME DEFAULT NULL");

        // Add employee data back
        foreach ($employees as $employee) {
            $this->addSql("UPDATE opithrm_employees SET date_of_birth = '" . $employee['dateOfBirth'] . "', joining_date = '" . $employee['joiningDate'] . "' WHERE id = " . $employee['id']);
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE opithrm_leaves DROP FOREIGN KEY FK_4B0AF95FF5EC012");
        $this->addSql("DROP TABLE opithrm_leave_request");
        $this->addSql("DROP TABLE opithrm_leaves");

        // Get old data
        $employees = $this->connection->fetchAll("SELECT id, date_of_birth, joining_date FROM opithrm_employees");

        $this->addSql("ALTER TABLE opithrm_employees ADD dateOfBirth DATE NOT NULL, ADD joiningDate DATE NOT NULL, DROP deletedAt, DROP date_of_birth, DROP joining_date, CHANGE number_of_children numberOfChildren INT NOT NULL");
        $this->addSql("ALTER TABLE opithrm_holiday_categories DROP deletedAt");
        $this->addSql("ALTER TABLE opithrm_users DROP FOREIGN KEY FK_8E744D498C03F15C");
        $this->addSql("DROP INDEX UNIQ_8E744D498C03F15C ON opithrm_users");
        $this->addSql("ALTER TABLE opithrm_users CHANGE employee_id employee INT DEFAULT NULL");
        $this->addSql("ALTER TABLE opithrm_users ADD CONSTRAINT FK_8E744D495D9F75A1 FOREIGN KEY (employee) REFERENCES opithrm_employees (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_8E744D495D9F75A1 ON opithrm_users (employee)");

        // Add employee data back
        foreach ($employees as $employee) {
            $this->addSql("UPDATE opithrm_employees SET dateOfBirth = '" . $employee['date_of_birth'] . "', joiningDate = '" . $employee['joining_date'] . "' WHERE id = " . $employee['id']);
        }
    }
}
