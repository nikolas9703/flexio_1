<?php

use \Flexio\Migration\Migration;

class InvItemsPreciosAlquilerMigration2 extends Migration
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
        $this->schema->create('inv_items_precios_alquiler', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('id_item');
            $table->integer('id_inv_precio');
             $table->decimal('hora',15,2);
            $table->decimal('diario',15,2);
            $table->decimal('semanal',15,2);
            $table->decimal('mensual',15,2);
            $table->decimal('tarifa_4_horas',15,2);
            $table->decimal('tarifa_15_dias',15,2);
            $table->decimal('tarifa_28_dias',15,2);
            $table->decimal('tarifa_30_dias',15,2);
        });
    }
}
