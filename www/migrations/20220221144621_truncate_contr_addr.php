<?php

use Phoenix\Migration\AbstractMigration;

class TruncateContrAddr extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("TRUNCATE TABLE contractors_has_addresses;");
    }

    protected function down(): void
    {
        
    }
}
