<?php

use Phoenix\Migration\AbstractMigration;

class RmBadTask extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
DELETE FROM tasks_geoobjects WHERE tasks_id = 26;
DELETE FROM tasks WHERE id = 26;
");
    }

    protected function down(): void
    {
        
    }
}
