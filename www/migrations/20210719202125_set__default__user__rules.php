<?php

use Phoenix\Migration\AbstractMigration;

class Set_default_user_rules extends AbstractMigration
{
    protected function up(): void
    {
        $rules = $this->fetch('acl_rule', ['id'], ['handle' => 'admin']);
        $users = $this->fetch('acl_user', ['id'], ['name' => 'admin', 'fullname' => 'admin'], ['id' => 'desc']);

        if (is_array($users) && $adminUserId = current($users)) {
            if (is_array($rules) && $adminRuleId = current($rules)) {
                $this->execute(
                    'INSERT INTO `acl_user_has_rules` (acl_user_id, acl_rule_id)
            VALUES ('.$adminUserId.', '.$adminRuleId.');'
                );
                ;
            }
        }
    }

    protected function down(): void
    {
        
    }
}
