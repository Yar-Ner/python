<?php

use Phoenix\Migration\AbstractMigration;

class AddPhotosToLocation extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `photos` 
ADD COLUMN `location_id` INT UNSIGNED NULL DEFAULT NULL AFTER `alarms_id`,
ADD INDEX `fkPLid_idx` (`location_id` ASC) VISIBLE;
ALTER TABLE `photos` 
ADD CONSTRAINT `fkPLid`
  FOREIGN KEY (`location_id`)
  REFERENCES `gps_location` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;
");
    }

    protected function down(): void
    {
        
    }
}
