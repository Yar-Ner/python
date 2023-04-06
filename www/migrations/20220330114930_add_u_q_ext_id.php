<?php

use Phoenix\Migration\AbstractMigration;

class AddUQExtId extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("
ALTER TABLE `tasks_orders` ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;
ALTER TABLE `tasks` ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;
ALTER TABLE `contractors` ADD UNIQUE INDEX `ext_id_UNIQUE` (`ext_id` ASC) VISIBLE;
");
    }

    protected function down(): void
    {
        
    }
}
