<?php

use \Flexio\Migration\Migration;

class FacturaCompraV2 extends Migration
{
    public function up()
    {
        $this->table('cre_creditos_aplicados')
        ->addColumn('acreditable_type', 'string', ['limit' => 300, 'default' => ''])
        ->addColumn('acreditable_id', 'integer', ['limit' => 10, 'default' => 0])
        ->addColumn('empresa_id', 'integer', ['limit' => 10, 'default' => 0])
        ->addColumn('total', 'decimal', ['scale' => 4, 'precision' => 16, 'default' => 0])
        ->addColumn('created_at', 'datetime')
        ->addColumn('updated_at', 'datetime')
        ->save();
    }

    public function down()
    {
        $this->dropTable('cre_creditos_aplicados');
    }
}
