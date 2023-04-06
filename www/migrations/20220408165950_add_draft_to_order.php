<?php

use Phoenix\Migration\AbstractMigration;

class AddDraftToOrder extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks_orders` CHANGE COLUMN `status` `status` ENUM('draft', 'queued', 'process', 'done', 'failed') NULL DEFAULT NULL ;
");
    }

    protected function down(): void
    {
        
    }
}
