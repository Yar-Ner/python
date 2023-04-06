<?php

use Phoenix\Migration\AbstractMigration;

class AddDeleteColumnAclUserAndAclUserGroup extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('ALTER TABLE `acl_user` ADD COLUMN `deleted` INT NULL DEFAULT NULL AFTER `last_login`;
                            ALTER TABLE `acl_user_group` ADD COLUMN `deleted` INT NULL DEFAULT NULL AFTER `description`');
    }

    protected function down(): void
    {
    }
}
