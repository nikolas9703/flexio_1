<?php

use \Flexio\Migration\Migration;

class ContactosV2 extends Migration
{
    public function up()
    {
        //VERIFICRA SI LA TABLA EXISTE
        $exist = $this->hasTable('cli_centros_facturacion');
        if($exist) {

          $table = $this->table('cli_centros_facturacion');

          //VERIFICAR SI LAS COLUMNAS EXISTEN
          //PARA EVITAR ERRROR DE DUPLICIDAD
          $column = $table->hasColumn('principal');
          if (!$column) {
            $table->addColumn('principal','integer',['default' => 0, 'limit' => 10])->save();
          }
        }
    }

    public function down()
    {
        $this->table('cli_centros_facturacion')
        ->removeColumn('principal')
        ->save();
    }
}
