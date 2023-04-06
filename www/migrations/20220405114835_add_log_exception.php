<?php

use Phoenix\Migration\AbstractMigration;

class AddLogException extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
        CREATE TABLE `log_exception` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `datetime` DATETIME NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `url` VARCHAR(1024) NULL DEFAULT NULL,
  `request` TEXT NULL DEFAULT NULL,
  `response` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id` ASC) INVISIBLE,
  CONSTRAINT `user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `acl_user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT);
");
    }

    protected function down(): void
    {
        
    }
}
