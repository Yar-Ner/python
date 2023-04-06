<?php

use Phoenix\Migration\AbstractMigration;

class DelGeoContrFromTA extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
ALTER TABLE `tasks_orders` 
DROP COLUMN `contractors_id`,
DROP COLUMN `geoobject_id`,
DROP INDEX `fkToGid_idx` ;
");
    }

    protected function down(): void
    {
        
    }
}
