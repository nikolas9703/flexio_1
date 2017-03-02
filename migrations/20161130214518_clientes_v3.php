<?php

use \Flexio\Migration\Migration;

class ClientesV3 extends Migration
{
    public function up()
    {
        //VERIFICRA SI LA TABLA EXISTE
        $exist = $this->hasTable('cli_cliente');
        if($exist) {

          $table = $this->table('cli_clientes');

          //VERIFICAR SI LAS COLUMNAS EXISTEN
          //PARA EVITAR ERRROR DE DUPLICIDAD
          $column = $table->hasColumn('detalle_identificacion');
          if (!$column) {
            $table->addColumn('detalle_identificacion','text',['default' => ''])->save();
          }

          $column = $table->hasColumn('retiene_impuesto');
          if (!$column) {
            $table->addColumn('retiene_impuesto','string',['default' => '', 'limit' => 140])->save();
          }

          $column = $table->hasColumn('lista_precio_venta_id');
          if (!$column) {
            $table->addColumn('lista_precio_venta_id','text',['default' => '', 'limit' => 10])->save();
          }

          $column = $table->hasColumn('lista_precio_alquiler_id');
          if (!$column) {
            $table->addColumn('lista_precio_alquiler_id','integer',['default' => '0', 'limit' => 10])->save();
          }

          $column = $table->hasColumn('termino_pago');
          if (!$column) {
            $table->addColumn('termino_pago','string',['default' => '', 'limit' => 140])->save();
          }
        }
    }

    public function down()
    {
        $this->table('cli_clientes')
        ->removeColumn('detalle_identificacion')
        ->removeColumn('retiene_impuesto')
        ->removeColumn('lista_precio_venta_id')
        ->removeColumn('lista_precio_alquiler_id')
        ->removeColumn('termino_pago')
        ->save();
    }
}
