<?php

use Phoenix\Migration\AbstractMigration;

class AddVehicleOdometer extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles` ADD COLUMN `odometer` INT NULL DEFAULT NULL AFTER `active`;");
    }

    protected function down(): void
    {
        
    }
}
