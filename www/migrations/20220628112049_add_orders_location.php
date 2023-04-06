<?php

use Phoenix\Migration\AbstractMigration;

class AddOrdersLocation extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `gps_location` ADD COLUMN `orders_id` INT NULL DEFAULT NULL AFTER `tasks_id`;");
    }

    protected function down(): void
    {
        
    }
}
