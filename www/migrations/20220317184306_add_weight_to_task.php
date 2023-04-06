<?php

use Phoenix\Migration\AbstractMigration;

class AddWeightToTask extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `tasks` 
ADD COLUMN `loaded_weight` FLOAT(10,4) NULL DEFAULT 0 AFTER `status`,
ADD COLUMN `empty_weight` FLOAT(10,4) NULL DEFAULT 0 AFTER `loaded_weight`;
");
    }

    protected function down(): void
    {
        
    }
}
