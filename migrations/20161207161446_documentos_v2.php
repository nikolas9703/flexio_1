<?php

use \Flexio\Migration\Migration;

class DocumentosV2 extends Migration
{
    public function up()
    {
        $this->table("doc_documentos")
        ->addColumn('centro_contable_id','integer',['limit' => 10, 'default' => 0])
        ->addColumn('fecha_documento','datetime')
        ->addColumn('tipo_id','integer',['limit' => 10, 'default' => 0])
        ->addColumn('etapa','string',['limit' => 140, 'default' => 'por_enviar'])
        ->addColumn('padre_id','integer',['limit' => 10, 'default' => 0])
        ->addColumn('archivado','integer',['limit' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table("doc_documentos")
        ->removeColumn('centro_contable_id')
        ->removeColumn('fecha_documento')
        ->removeColumn('tipo_id')
        ->removeColumn('etapa')
        ->removeColumn('padre_id')
        ->removeColumn('archivado')
        ->save();
    }
}
