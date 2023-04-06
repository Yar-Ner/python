<?php

use Phoenix\Migration\AbstractMigration;

class TruncateDb extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
        TRUNCATE photos;
        TRUNCATE tasks_orders;
        TRUNCATE contractors_has_geoobjects;
        TRUNCATE tasks_geoobjects;
        DROP TABLE tasks_addresses;
        DROP TABLE vehicles_has_tasks;
        SET SQL_SAFE_UPDATES = 0;
        DELETE FROM tasks ;
        SET SQL_SAFE_UPDATES = 1;
        ");
    }

    protected function down(): void
    {
        
    }
}
