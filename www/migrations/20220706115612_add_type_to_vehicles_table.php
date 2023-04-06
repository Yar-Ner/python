<?php

use Phoenix\Migration\AbstractMigration;

class AddTypeToVehiclesTable extends AbstractMigration
{
  protected function up(): void
  {
    $this->execute("ALTER TABLE `vehicles` 
ADD COLUMN `type` INT NULL DEFAULT NULL AFTER `number`,
ADD INDEX `fkVVTid_idx` (`type` ASC) VISIBLE;
ALTER TABLE `vehicles` 
ADD CONSTRAINT `fkVVTid`
  FOREIGN KEY (`id`)
  REFERENCES `vehicles_types` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;");
  }

  protected function down(): void
  {

  }
}
