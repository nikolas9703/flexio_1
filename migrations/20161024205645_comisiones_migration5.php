<?php

use \Flexio\Migration\Migration;

class ComisionesMigration5 extends Migration
{
  public function up()
 {

      $this->schema->create('com_comisiones', function(Illuminate\Database\Schema\Blueprint $table) {
         $table->increments('id');
         $table->binary('uuid_comision');
         $table->string('numero');
         $table->integer('centro_contable_id');
         $table->integer('area_negocio_id');
         $table->binary('uuid_cuenta_activo');
         $table->integer('metodo_pago');
         $table->datetime('fecha_pago');
         $table->integer('empresa_id');
         $table->datetime('fecha_creacion');
         $table->integer('estado_id');
         $table->integer('activo');
         $table->string('descripcion');
         $table->datetime('fecha_programada_pago');
         $table->timestamps();
     });


 }

 /**
  * Reverse the migrations.
  *
  * @return void
  */
 public function down()
 {
     $this->schema->drop('com_comisiones');
 }
}
