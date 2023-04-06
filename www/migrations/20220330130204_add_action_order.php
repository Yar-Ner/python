<?php

use Phoenix\Migration\AbstractMigration;

class AddActionOrder extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks_orders` 
CHANGE COLUMN `action` `action` ENUM('deliver', 'change', 'grab', 'delivergrab', 'grabdeliver', 'transportation') NULL DEFAULT NULL ;
");
    }

    protected function down(): void
    {
        
    }
}
