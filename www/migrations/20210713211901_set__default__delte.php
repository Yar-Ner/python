<?php

use Phoenix\Migration\AbstractMigration;

class Set_default_delte extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('ALTER TABLE `vehicles` CHANGE COLUMN `deleted` `deleted` TINYINT NOT NULL DEFAULT 0 ;');
    }

    protected function down(): void
    {
        
    }
}
