<?php

use \Flexio\Migration\Migration;

class ContactosV1 extends Migration
{
    public function up()
    {
      //VERIFICRA SI LA TABLA EXISTE
      $exist = $this->hasTable('con_contactos');
      if($exist) {

        $table = $this->table('con_contactos');

        //VERIFICAR SI LAS COLUMNAS EXISTEN
        //PARA EVITAR ERRROR DE DUPLICIDAD
        $column = $table->hasColumn('detalle_identificacion');
        if (!$column) {
          $table->addColumn('detalle_identificacion','text',['default' => ''])->save();
        }
      }
    }

    public function down()
    {
        $this->table('con_contactos')
        ->removeColumn('detalle_identificacion')
        ->save();
    }
}
