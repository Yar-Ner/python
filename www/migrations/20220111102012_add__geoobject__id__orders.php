<?php

use Phoenix\Migration\AbstractMigration;

class AddGeoobjectIdOrders extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks_orders` 
ADD COLUMN `geoobject_id` INT UNSIGNED NULL DEFAULT NULL AFTER `comment`,
ADD INDEX `fkToGid_idx` (`geoobject_id` ASC) VISIBLE;
;
ALTER TABLE `tasks_orders` 
ADD CONSTRAINT `fkToGid`
  FOREIGN KEY (`geoobject_id`)
  REFERENCES `geoobjects` (`id`)
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT;
");
    }

    protected function down(): void
    {
        
    }
}
