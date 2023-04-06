<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnContractorIdAddresses extends AbstractMigration
{
    protected function up(): void
    {
      $table = $this->table('addresses');
      $table->addColumn('contractor_id', 'integer', ['after' => 'radius'])->save();
    }

    protected function down(): void
    {
        
    }
}
