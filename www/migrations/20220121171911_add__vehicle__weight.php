<?php

use Phoenix\Migration\AbstractMigration;

class AddVehicleWeight extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles` ADD COLUMN `weight` INT NULL DEFAULT NULL AFTER `color`;");
    }

    protected function down(): void
    {
        
    }
}
