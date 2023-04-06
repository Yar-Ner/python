<?php

use Phoenix\Migration\AbstractMigration;

class FillTestData extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("
                        INSERT INTO `addresses` SET `ext_id` = 'выф', `address` = 'павлова', `deleted` = '0';
                        SET @lastInsertIdAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_addresses` SET `tasks_id` = '5', `address_id` = @lastInsertIdAddresses;
                        
                        SET @lastInsertIdTasksAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_orders` SET `task_addresses_id` = @lastInsertIdTasksAddresses, `ext_id` = '1', `action` = 'deliver', `weight` = '321.0000', `package_weight` = '321.0000';
                        INSERT INTO `tasks_orders` SET `task_addresses_id` = @lastInsertIdTasksAddresses, `ext_id` = '51', `action` = 'deliver', `weight` = '1851.0000', `gross_weight` = '0', `package_weight` = '0', `status` = '0', `failed_reason` = '0';

                        INSERT INTO `addresses` SET `ext_id` = '12-12', `address` = 'г. Ярославль, Мышкинский проезд', `lat` = '47.234219', `long` = '47.234219', `radius` = '1.200000', `deleted` = '0';
                        SET @lastInsertIdAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_addresses` SET `tasks_id` = '6', `address_id` = @lastInsertIdAddresses;
                        INSERT INTO `tasks_addresses` SET `tasks_id` = '5', `address_id` = @lastInsertIdAddresses, `type` = 'start';
                        
                        SET @lastInsertIdTasksAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_orders` SET `task_addresses_id` = (SELECT LAST_INSERT_ID()), `ext_id`  = '5', `action` = 'deliver', `weight` = '156', `gross_weight` = '0', `package_weight` = '0', `status` = '0';
                        
                        INSERT INTO `addresses` SET `ext_id` = 'ывф', `address` = 'менделева', `deleted` = '0';
                        SET @lastInsertIdAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_addresses` SET `tasks_id` = '5', `address_id` = @lastInsertIdAddresses;
                        INSERT INTO `tasks_addresses` SET `tasks_id` = '6', `address_id` = @lastInsertIdAddresses;
                        
                        
                        SET @lastInsertIdTasksAddresses = (SELECT LAST_INSERT_ID());
                        INSERT INTO `tasks_orders` SET `task_addresses_id` = @lastInsertIdTasksAddresses, `ext_id` = '221', `action` = 'deliver', `weight` = '12', `gross_weight` = '13', `package_weight` = '2', `plan_arrival` = '2021-05-21 18:10:00', `plan_departure` = '2021-05-21 18:10:00', `fact_arrival` = '2021-05-21 18:10:00', `face_departure` = '2021-05-21 18:10:00', `payload` = '{}';
                        
                        INSERT INTO `addresses` SET `ext_id` = 'ывф', `address` = 'гагарина', `deleted` = '0';
      ");
    }

    protected function down(): void
    {
        
    }
}
