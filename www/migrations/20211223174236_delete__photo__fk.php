<?php

use Phoenix\Migration\AbstractMigration;

class DeletePhotoFk extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `vehicles_alarms` 
DROP FOREIGN KEY `fkVaPid`;
");
    }

    protected function down(): void
    {
        
    }
}
