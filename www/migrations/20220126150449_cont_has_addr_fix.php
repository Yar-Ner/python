<?php

use Phoenix\Migration\AbstractMigration;

class ContHasAddrFix extends AbstractMigration
{
    protected function up(): void
    {
      $this->execute("DROP TABLE `contractors_has_adresses`;
      CREATE TABLE `contractors_has_addresses` (
    `contractors_id` INT UNSIGNED NOT NULL,
  `addresses_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`contractors_id`, `addresses_id`),
  INDEX `fkCAAid_idx` (`addresses_id` ASC) VISIBLE,
  CONSTRAINT `fkCACid`
    FOREIGN KEY (`contractors_id`)
    REFERENCES `contractors` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fkCAAid`
    FOREIGN KEY (`addresses_id`)
    REFERENCES `addresses` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT);");

    }

    protected function down(): void
    {
        
    }
}
