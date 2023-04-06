<?php

use Phoenix\Migration\AbstractMigration;

class Add_color_vehicle extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles` 
ADD COLUMN `color` VARCHAR(45) NULL DEFAULT NULL AFTER `description`;
");
    }

    protected function down(): void
    {
        
    }
}
