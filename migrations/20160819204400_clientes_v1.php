<?php

use \Flexio\Migration\Migration;

class ClientesV1 extends Migration
{

    protected $tableName = 'cli_clientes';

    public function up(){
      $tabla = $this->table($this->tableName);
      $column = $tabla->hasColumn('credito_usado');

      if (!$column) {
        $this->table($this->tableName)
                ->addColumn('credito_usado', 'decimal', ['scale'=>4,'precision'=>10,'default'=>0])
                ->save();
      }
    }

    public function down(){

        $this->table($this->tableName)
                ->removeColumn('credito_usado')
                ->save();

    }

}
