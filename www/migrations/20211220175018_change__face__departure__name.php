<?php

use Phoenix\Migration\AbstractMigration;

class Change_face_departure_name extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks_orders` 
CHANGE COLUMN `face_departure` `fact_departure` DATETIME NULL DEFAULT NULL ;
");
    }

    protected function down(): void
    {
        
    }
}
