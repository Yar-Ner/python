<?php

use Phoenix\Migration\AbstractMigration;

class AddExtIdVehicles extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles` 
ADD COLUMN `ext_id` VARCHAR(255) NULL DEFAULT NULL AFTER `id`,
ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;
;
");
    }

    protected function down(): void
    {
        
    }
}
