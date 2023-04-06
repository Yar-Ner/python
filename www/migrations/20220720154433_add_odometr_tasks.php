<?php

use Phoenix\Migration\AbstractMigration;

class AddOdometrTasks extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
		ALTER TABLE `tasks` ADD COLUMN `odometer` INT NULL DEFAULT NULL AFTER `distance`;
	");
    }

protected function down(): void
    {
        
    }
}
