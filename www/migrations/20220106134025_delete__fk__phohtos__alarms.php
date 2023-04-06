<?php

use Phoenix\Migration\AbstractMigration;

class DeleteFkPhotosAlarms extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `photos` 
DROP FOREIGN KEY `fkPAid`;
ALTER TABLE `photos` 
DROP INDEX `fkPAid_idx` ;
;
");
    }

    protected function down(): void
    {
        
    }
}
