<?php

use Phoenix\Migration\AbstractMigration;

class ClearTasksTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
        set @var=if ((SELECT true FROM information_schema.TABLE_CONSTRAINTS
			WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_NAME='photos'
            AND CONSTRAINT_NAME='fkPTId') = true,'ALTER TABLE `photos` DROP FOREIGN KEY `fkPTId`;','select 1');
        
        TRUNCATE TABLE tasks_orders;
        TRUNCATE TABLE tasks_addresses;
        TRUNCATE TABLE photos;
        ALTER TABLE `vehicles_alarms` DROP FOREIGN KEY `fkVaVid`; #1
        ALTER TABLE `vehicles_alarms` DROP INDEX `fkVaVid_idx` ;
        ALTER TABLE `vehicles_alarms` DROP FOREIGN KEY `fkVALid`; #2
        ALTER TABLE `vehicles_alarms` DROP INDEX `fkVALid_idx` ;
        TRUNCATE TABLE gps_location;
        prepare stmt from @var; #3
		execute stmt;
		deallocate prepare stmt;
        ALTER TABLE `tasks_addresses` DROP FOREIGN KEY `fkTaTid`; #4
        ALTER TABLE `tasks_addresses` DROP INDEX `fkTaTid_idx`;
        ALTER TABLE `vehicles_has_tasks` DROP FOREIGN KEY `fkTsk`; #5
        ALTER TABLE `vehicles_has_tasks` DROP INDEX `fkTsk_idx` ;
        TRUNCATE TABLE tasks;
        
        ALTER TABLE `tasks` CHANGE COLUMN `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ;
        ALTER TABLE `vehicles_alarms` ADD INDEX `fkVaVid_idx` (`vehicles_id` ASC) VISIBLE; #1
        ALTER TABLE `vehicles_alarms` ADD CONSTRAINT `fkVaVid`
          FOREIGN KEY (`vehicles_id`)
          REFERENCES `vehicles` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;
        ALTER TABLE `photos` CHANGE COLUMN `tasks_id` `orders_id` INT UNSIGNED NULL DEFAULT NULL ; #3
        ALTER TABLE `photos` ADD CONSTRAINT `fkPOid`
          FOREIGN KEY (`orders_id`)
          REFERENCES `tasks_orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;
        ALTER TABLE `tasks_addresses` ADD CONSTRAINT `fkTaTid` #4
          FOREIGN KEY (`tasks_id`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;
        ALTER TABLE `vehicles_has_tasks` ADD INDEX `fkTsk_idx` (`tasks_id` ASC) VISIBLE; #5
        ALTER TABLE `vehicles_has_tasks` 
        ADD CONSTRAINT `fkTsk`
          FOREIGN KEY (`tasks_id`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;
        ");
    }

    protected function down(): void
    {
        
    }
}
