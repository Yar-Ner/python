<?php

use Phoenix\Migration\AbstractMigration;

class AddContractorsIdColumn extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("ALTER TABLE `tasks_orders` 
ADD COLUMN `contractors_id` INT NULL AFTER `comment`;
SET SQL_SAFE_UPDATES = 0;
UPDATE `tasks_orders` SET `contractors_id` = 1;
SET SQL_SAFE_UPDATES = 1;
");
    }

    protected function down(): void
    {
        
    }
}
