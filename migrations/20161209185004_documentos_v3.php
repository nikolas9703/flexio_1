<?php

use \Flexio\Migration\Migration;

class DocumentosV3 extends Migration
{
    public function up()
    {


        // inserting multiple rows
        $rows = [
            ['key' => '1', 'valor' => 'Por enviar', 'etiqueta' => 'por_enviar', 'tipo' => 'estado', 'modulo' => 'documentos', 'orden' => '2', 'activo' => 1],
            ['key' => '2', 'valor' => 'No se env&iacute;a', 'etiqueta' => 'no_se_envia', 'tipo' => 'estado', 'modulo' => 'documentos', 'orden' => '4', 'activo' => 1],
            ['key' => '3', 'valor' => 'Enviado', 'etiqueta' => 'enviado', 'tipo' => 'estado', 'modulo' => 'documentos', 'orden' => '6', 'activo' => 1]
        ];

        // this is a handy shortcut
        $this->insert('flexio_catalogos', $rows);
    }

    public function down()
    {
        $this->execute("DELETE FROM flexio_catalogos WHERE `key` in (1,2,3) AND modulo ='documentos'");
    }
}
