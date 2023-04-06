<?php

use Phoenix\Migration\AbstractMigration;

class VehiclesHasContainers extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("CREATE TABLE `vehicles_has_containers` (
  `vehicle_id` INT UNSIGNED NOT NULL,
  `container_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`vehicle_id`, `container_id`),
  INDEX `fkVHCCid_idx` (`container_id` ASC) INVISIBLE,
  CONSTRAINT `fkVHCVid`
    FOREIGN KEY (`vehicle_id`)
    REFERENCES `vehicles` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fkVHCCid`
    FOREIGN KEY (`container_id`)
    REFERENCES `vehicles_containers` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT);
");
    }

    protected function down(): void
    {
        
    }
}
