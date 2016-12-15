<?php

use \Flexio\Migration\Migration;

class PlanillaConfiguracionMigration extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $exists = $this->schema->hasTable('pln_config_liquidaciones_pagos');
        if(!$exists){
            $this->schema->create('pln_config_liquidaciones_pagos', function(Illuminate\Database\Schema\Blueprint $table) {
                    $table->increments('id');
                    $table->integer('liquidacion_id');
                    $table->integer('tipo_pago_id');
                    $table->timestamps();
            });   
        }
    	
    }
}
