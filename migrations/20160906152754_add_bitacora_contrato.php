<?php

use \Flexio\Migration\Migration;

class AddBitacoraContrato extends Migration
{
 
    public function change()
    {
        $this->schema->create('conalq_contratos_alquiler_historial', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_historial');
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('codigo_cuenta');
            $table->integer('empresa_id');
            $table->integer('contrato_id');
            $table->integer('usuario_id');
            $table->text('antes');
            $table->text('despues');
            $table->enum('tipo', ['creado', 'actualizado', 'comentario'])->default("actualizado");
            
             
                     
             $table->timestamps();
        });
    }
}
