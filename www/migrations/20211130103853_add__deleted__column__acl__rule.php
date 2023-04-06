<?php

use Phoenix\Migration\AbstractMigration;

class Add_deleted_column_acl_rule extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute('ALTER TABLE `acl_rule` ADD COLUMN `deleted` INT NULL DEFAULT NULL AFTER `updated`');
    }

    protected function down(): void
    {
        
    }
}
