<?php

use Phoenix\Migration\AbstractMigration;

class AddMessageType extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("ALTER TABLE `chat_messages` ADD COLUMN `type` INT NULL DEFAULT NULL AFTER `content`;");
    }

    protected function down(): void
    {
        
    }
}
