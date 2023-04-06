<?php

use Phoenix\Migration\AbstractMigration;

class AddVehicleTypes extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("CREATE TABLE `vehicles_types` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(255) NULL,
  `deleted` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`));");
    }

    protected function down(): void
    {
        
    }
}
