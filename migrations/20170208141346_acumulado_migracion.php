<?php

use \Flexio\Migration\Migration;

class AcumuladoMigracion extends Migration
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
        $this->schema->create('pln_base_acumulados', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('acumulado_id')->unsigned();
            $table->integer('colaborador_id')->unsigned();
            $table->decimal('acumulado_original',15,4);
            $table->decimal('acumulado_usado',15,4);
            $table->decimal('acumulado_planilla',15,4);
            $table->enum('estado',['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

}
