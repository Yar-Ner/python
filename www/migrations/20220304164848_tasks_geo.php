<?php

use Phoenix\Migration\AbstractMigration;

class TasksGeo extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
CREATE TABLE `tasks_geoobjects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tasks_id` INT UNSIGNED NOT NULL,
  `geoobjects_id` INT UNSIGNED NOT NULL,
  `order` INT NULL DEFAULT NULL,
  `trip_type` ENUM('start', 'middle', 'finish', 'return') NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `TGTid_idx` (`tasks_id` ASC) VISIBLE,
  INDEX `TGGid_idx` (`geoobjects_id` ASC) VISIBLE,
  CONSTRAINT `TGTid`
    FOREIGN KEY (`tasks_id`)
    REFERENCES `makrab`.`tasks` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `TGGid`
    FOREIGN KEY (`geoobjects_id`)
    REFERENCES `makrab`.`geoobjects` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT);
");
    }

    protected function down(): void
    {
        
    }
}
