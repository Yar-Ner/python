<?php

use Phoenix\Migration\AbstractMigration;

class Unique_contactor_ext_id extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('ALTER TABLE `contractors` ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;');
    }

    protected function down(): void
    {
        $this->execute('ALTER TABLE `contractors` DROP INDEX `ext_id_UNIQUE`');
        
    }
}
