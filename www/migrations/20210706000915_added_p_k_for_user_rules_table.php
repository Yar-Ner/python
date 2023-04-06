<?php

use Phoenix\Migration\AbstractMigration;

class AddedPKForUserRulesTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('ALTER TABLE `acl_user_has_rules` ADD PRIMARY KEY (`acl_user_id`, `acl_rule_id`)');
    }

    protected function down(): void
    {
        $this->execute('ALTER TABLE `acl_user_has_rules` DROP PRIMARY KEY;');
    }
}
