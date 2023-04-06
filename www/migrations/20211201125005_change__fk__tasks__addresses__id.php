<?php

use Phoenix\Migration\AbstractMigration;

class ChangeFkTasksAddressesId extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("ALTER TABLE `tasks_orders` 
DROP FOREIGN KEY `fkToTid`;
ALTER TABLE `tasks_orders` 
ADD INDEX `fkToTid_idx` (`task_addresses_id` ASC) VISIBLE,
DROP INDEX `fkToTid_idx` ;
;
ALTER TABLE `tasks_orders` 
ADD CONSTRAINT `fkToTid`
  FOREIGN KEY (`task_addresses_id`)
  REFERENCES `tasks_addresses` (`id`);");
    }

    protected function down(): void
    {
        
    }
}
