<?php

use \Flexio\Migration\Migration;

class CreacionBuscadorLista extends Migration
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
        $this->schema->create('flexio_busqueda', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('busqueda',250);
            $table->text('campos');
            $table->string('modulo');
            $table->integer('usuario_id');
            $table->integer('empresa_id');
            $table->timestamps();
        });
    }
}
