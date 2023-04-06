<?php

use Phoenix\Migration\AbstractMigration;

class Contactor_default_deleted_0 extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('ALTER TABLE `contractors` CHANGE COLUMN `deleted` `deleted` TINYINT NULL DEFAULT 0 ;');
    }

    protected function down(): void
    {
        
    }
}
