<?php

use Phoenix\Migration\AbstractMigration;

class RmBadGeos extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
DELETE FROM contractors_has_geoobjects WHERE geoobjects_id IN(27,28,29);
DELETE FROM tasks_geoobjects WHERE geoobjects_id IN(28,29);
DELETE FROM geoobjects WHERE id IN (24,25,26,27,28,29);
");
    }

    protected function down(): void
    {
        
    }
}
