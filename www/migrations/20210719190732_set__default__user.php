<?php

use Phoenix\Migration\AbstractMigration;

class Set_default_user extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute(
            'INSERT INTO `acl_user` (name, fullname, password, state, created) 
            VALUES ("admin", "admin", sha1(md5(concat(md5(md5("admin")), ";Ej>]sjkip"))), 1, NOW());'
        );
    }

    protected function down(): void
    {
        
    }
}
