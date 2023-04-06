<?php

use Phoenix\Migration\AbstractMigration;

class UpdateSettings extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute("TRUNCATE TABLE cfg_setting");

        $settingArray = ['updateTime', 'logistPhone', 'carEmptyWeight',
          'arbitraryExecutionTasks', 'chooseWeightTare', 'closeTaskWithoutPhotos',
          'allOrdersComplete', 'geoRadius', 'fixTime', 'countOfAttempt', 'countOfAttemptForCheckingInRadius'];

        foreach($settingArray as $setting) {
          $this->execute("INSERT INTO `cfg_setting` (`handle`, `val`, `updated`) VALUES ('$setting', '1', now());");
        }

        $this->execute("INSERT INTO `cfg_user_setting` (`acl_user_id`, `handle`, `val`, `updated`) VALUES (15, 'refresh.interval', '1', now());
INSERT INTO `cfg_user_setting` (`acl_user_id`, `handle`, `val`, `updated`) VALUES (1, 'refresh.interval', '1', now());
INSERT INTO `cfg_user_setting` (`acl_user_id`, `handle`, `val`, `updated`) VALUES (13, 'refresh.interval', '1', now());
INSERT INTO `cfg_user_setting` (`acl_user_id`, `handle`, `val`, `updated`) VALUES (14, 'refresh.interval', '1', now());");
    }

    protected function down(): void
    {
        
    }
}
