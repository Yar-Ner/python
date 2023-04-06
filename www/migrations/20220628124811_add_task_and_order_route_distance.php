<?php

use Phoenix\Migration\AbstractMigration;

class AddTaskAndOrderDistance extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
ALTER TABLE `tasks_orders` ADD COLUMN `distance` INT NULL DEFAULT NULL AFTER `comment`;
ALTER TABLE `tasks` ADD COLUMN `distance` INT NULL DEFAULT NULL AFTER `updated`;");
    }

    protected function down(): void
    {
        
    }
}
