<?php

use Phoenix\Migration\AbstractMigration;

class ChangeAdress extends AbstractMigration
{
    protected function up(): void
    {
     $this->execute("
     ALTER TABLE `contractors_has_adresses` 
DROP FOREIGN KEY `fkCAAid`;
ALTER TABLE `contractors_has_adresses` 
DROP COLUMN `adresses_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`contractors_id`),
DROP INDEX `fkCAAid_idx` ;
;

ALTER TABLE `contractors_has_adresses` 
ADD COLUMN `addresses_id` INT UNSIGNED NOT NULL AFTER `contractors_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`contractors_id`, `addresses_id`);
;

ALTER TABLE `contractors_has_adresses` 
ADD INDEX `fkCAAid_idx` (`addresses_id` ASC) VISIBLE;
;
ALTER TABLE `contractors_has_adresses` 
ADD CONSTRAINT `fkCAAid`
  FOREIGN KEY (`addresses_id`)
  REFERENCES `addresses` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `contractors_has_adresses` 
ADD INDEX `fkCACid_idx` (`contractors_id` ASC) VISIBLE;
;

ALTER TABLE `contractors_has_adresses` 
RENAME TO  `contractors_has_addresses`;
");
    }

    protected function down(): void
    {
        
    }
}
