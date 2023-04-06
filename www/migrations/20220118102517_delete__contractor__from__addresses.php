<?php

use Phoenix\Migration\AbstractMigration;

class DeleteContractorFromAddresses extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `addresses` 
DROP COLUMN `contractor_id`;
");
    }

    protected function down(): void
    {
        
    }
}


