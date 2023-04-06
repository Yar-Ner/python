<?php

use Phoenix\Migration\AbstractMigration;

class AddActiveToVehicle extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles` ADD COLUMN `active` TINYINT(1) NULL DEFAULT '1' AFTER `weight`;");
    }

    protected function down(): void
    {
        
    }
}
