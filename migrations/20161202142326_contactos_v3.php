<?php

use \Flexio\Migration\Migration;

class ContactosV3 extends Migration
{
    public function up()
    {
        //VERIFICRA SI LA TABLA EXISTE
        $exist = $this->hasTable('cli_centros_facturacion');
        if($exist) {

          $table = $this->table('cli_centros_facturacion');

          //VERIFICAR SI LAS COLUMNAS EXISTEN
          //PARA EVITAR ERRROR DE DUPLICIDAD
          $column = $table->hasColumn('provincia_id');
          if (!$column) {
            $table->addColumn('provincia_id', 'integer', ['limit' => 10, 'default' => 0])->save();
          }

          $column = $table->hasColumn('distrito_id');
          if (!$column) {
            $table->addColumn('distrito_id', 'integer', ['limit' => 10, 'default' => 0])->save();
          }

          $column = $table->hasColumn('corregimiento_id');
          if (!$column) {
            $table->addColumn('corregimiento_id', 'integer', ['limit' => 10, 'default' => 0])->save();
          }

          $column = $table->hasColumn('eliminado');
          if (!$column) {
            $table->addColumn('eliminado', 'integer', ['limit' => 10, 'default' => 0])->save();
          }

        }
    }

    public function down()
    {
        $this->table('cli_centros_facturacion')
        ->removeColumn('provincia_id')
        ->removeColumn('distrito_id')
        ->removeColumn('corregimiento_id')
        ->removeColumn('eliminado', 'integer')
        ->save();
    }
}
