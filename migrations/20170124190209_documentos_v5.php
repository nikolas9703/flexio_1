<?php

use \Flexio\Migration\Migration;

class DocumentosV5 extends Migration
{
    public function up()
    {
        $table = $this->table('comentarios');
        if(!$table->hasColumn("centro_contable_id")){    
        $table->addColumn('centro_contable_id', 'integer', ['limit' => 10, 'default' => 0])
        ->save();
        }    
    }

    public function down()
    {
        $this->table('comentarios')
        ->removeColumn('centro_contable_id')
        ->save();
    }
}
