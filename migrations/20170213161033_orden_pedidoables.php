<?php

use \Flexio\Migration\Migration;

class OrdenPedidoables extends Migration
{
  public function up(){
    //verificar si no existe la tabla
    $exist = $this->hasTable('ord_pedidoables');
    if(!$exist) {
      $this->table('ord_pedidoables')
          ->addColumn('orden_id','integer',['limit'=>11])
          ->addColumn('ord_pedidoable_id','integer',['limit'=>11])
          ->addColumn('ord_pedidoable_type','string',['limit'=>140])
          ->addIndex(array('orden_id','ord_pedidoable_id','ord_pedidoable_type'))
          ->save();
      }
  }

  public function down(){
    $exist = $this->hasTable('ord_pedidoables');
    if($exist) {
      $this->dropTable('ord_pedidoables')->save();
    }
  }
}
