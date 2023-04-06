<?php

use Phoenix\Migration\AbstractMigration;

class Add_volume_orders extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks_orders` 
ADD COLUMN `volume` FLOAT(10,4) NULL DEFAULT NULL AFTER `action`;
");
    }

    protected function down(): void
    {
        
    }
}
