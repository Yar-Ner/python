<?php

use Phoenix\Migration\AbstractMigration;

class Insert_rules extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute(
            'INSERT INTO `acl_rule` (name, handle) VALUES ("Администратор", "admin"), ("Логист", "logist");'
        );
    }

    protected function down(): void
    {
        
    }
}
