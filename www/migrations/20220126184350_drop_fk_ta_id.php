<?php

use Phoenix\Migration\AbstractMigration;

class DropFkTaId extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("
set @var=if ((SELECT true FROM information_schema.TABLE_CONSTRAINTS
			WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_NAME='tasks_orders') = true,'ALTER TABLE `tasks_orders` 
DROP FOREIGN KEY `fkToTid`;
ALTER TABLE `tasks_orders` DROP INDEX `fkToTid_idx` ;','select 1');

prepare stmt from @var;
execute stmt;
deallocate prepare stmt;
;");

    }

    protected function down(): void
    {
        
    }
}
