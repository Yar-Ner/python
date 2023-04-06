<?php

use Phoenix\Migration\AbstractMigration;

class ChangeFkVehicleAlarmsGps extends AbstractMigration
{
    protected function up(): void
    {
     $this->execute("ALTER TABLE `vehicles_alarms` 
DROP FOREIGN KEY `fkVALid`;
ALTER TABLE `vehicles_alarms` 
ADD CONSTRAINT `fkVaLid`
  FOREIGN KEY (`location_id`)
  REFERENCES `gps_location` (`id`);
");
    }

    protected function down(): void
    {
        
    }
}
