<?php

use Phoenix\Migration\AbstractMigration;

class TestMigrate extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('SELECT 1');
    }

    protected function down(): void
    {
    }
}
