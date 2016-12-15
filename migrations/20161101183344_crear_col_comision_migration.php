<?php

use \Flexio\Migration\Migration;

class CrearColComisionMigration extends Migration
{

     public function tabla_com_colaborador_acumulado()
     {
         if (!$this->schema->hasTable('com_colaborador_acumulado')) {
              $this->schema->create('com_colaborador_acumulado', function(Illuminate\Database\Schema\Blueprint $table) {
                 $table->increments('id');
                 $table->integer('com_colaborador_id');
                 $table->integer('com_acumulado_id');
                 $table->decimal('monto',15,2);
              });
        }

     }
     public function tabla_com_colaborador_deduccion()
     {
         if (!$this->schema->hasTable('com_colaborador_deduccion')) {
             $this->schema->create('com_colaborador_deduccion', function(Illuminate\Database\Schema\Blueprint $table) {
                 $table->increments('id');
                 $table->integer('com_colaborador_id');
                 $table->integer('com_deduccion_id');
                 $table->decimal('monto',15,2);
              });
        }

     }

    public function up()
    {
        $this->tabla_com_colaborador_acumulado();
        $this->tabla_com_colaborador_deduccion();
    }
}
