<?php

use Phoenix\Migration\AbstractMigration;

class AddUserToTask extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks` 
ADD COLUMN `user_id` INT NULL DEFAULT NULL AFTER `id`,
ADD INDEX `fkTUid_idx` (`user_id` ASC) VISIBLE;
ALTER TABLE `tasks` 
ADD CONSTRAINT `fkTUid`
  FOREIGN KEY (`id`)
  REFERENCES `makrab`.`acl_user` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;");
    }

    protected function down(): void
    {
        
    }
}
