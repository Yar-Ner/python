<?php

use Phoenix\Migration\AbstractMigration;

class InsertAlarms extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("INSERT INTO alarms SET type = 'das', icon = 'wheel', name = 'Прокол колеса', color = 'black', deleted = '0';
INSERT INTO alarms SET type = 'barrier', icon = 'barrier', name = 'Сломался шлагбаум', color = 'blue', deleted = '0';
INSERT INTO alarms SET type = 'dsa', icon = 'roadAccident', name = 'ДТП', color = 'red', deleted = '0';
INSERT INTO alarms SET type = 'sda', icon = 'carcase', name = 'Неисправность кузова', color = 'green', deleted = '0';
INSERT INTO alarms SET type = 'dsa', icon = 'situation1', name = 'Ситуация 1', color = 'yellow', deleted = '0';");
    }

    protected function down(): void
    {
        
    }
}
