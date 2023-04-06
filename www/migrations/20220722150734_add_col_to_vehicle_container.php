<?php

use Phoenix\Migration\AbstractMigration;

class AddColToVehicleContainer extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles_containers` 
ADD COLUMN `ext_id` VARCHAR(255) NULL DEFAULT NULL AFTER `id`,
ADD COLUMN `units` VARCHAR(45) NULL DEFAULT NULL AFTER `description`,
ADD COLUMN `volume` FLOAT NULL DEFAULT NULL AFTER `units`,
ADD COLUMN `dropped_out` TINYINT NULL DEFAULT 0 AFTER `volume`,
ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;
");
    }

    protected function down(): void
    {
        
    }
}
