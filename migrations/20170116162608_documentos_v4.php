<?php

use \Flexio\Migration\Migration;

class DocumentosV4 extends Migration
{
    public function up()
    {
        $exist = $this->hasTable('doc_documentos');
        if($exist) {

          $tabla = $this->table('doc_documentos');

          //Verificar si el campo ya existe
          $column = $tabla->hasColumn('deleted_at');
          if (!$column) {

            //Agregar campo
            $tabla->addColumn('deleted_at', 'datetime', ['null' => true])->save();
          }
        }
    }

    public function down()
    {
        $this->table('doc_documentos')
        ->removeColumn('deleted_at')
        ->save();
    }
}
