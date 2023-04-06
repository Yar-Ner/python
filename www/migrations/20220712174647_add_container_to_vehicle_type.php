<?php

use Phoenix\Migration\AbstractMigration;

class AddContainerToVehicleType extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
DROP TABLE `vehicles_has_containers`;
CREATE TABLE `vehicle_types_has_containers` (
  `vehicle_types_id` INT UNSIGNED NOT NULL,
  `container_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`vehicle_types_id`, `container_id`),
  INDEX `fk_VT_idx` (`vehicle_types_id` ASC) INVISIBLE,
  INDEX `fk_C_idx` (`container_id` ASC) VISIBLE,
  CONSTRAINT `fk_VT`
    FOREIGN KEY (`vehicle_types_id`)
    REFERENCES `vehicles_types` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_C`
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
