<?php

use Phoenix\Migration\AbstractMigration;

class Rename_tasks_id extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("ALTER TABLE `tasks_orders` 
DROP FOREIGN KEY `fkToTid`;
ALTER TABLE `tasks_orders` 
CHANGE COLUMN `tasks_id` `task_addresses_id` INT UNSIGNED NOT NULL ;
ALTER TABLE `tasks_orders` 
ADD CONSTRAINT `fkToTid`
  FOREIGN KEY (`task_addresses_id`)
  REFERENCES `tasks` (`id`);");
    }

    protected function down(): void
    {
        
    }
}
