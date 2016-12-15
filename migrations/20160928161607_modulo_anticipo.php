<?php

use \Flexio\Migration\Migration;

class ModuloAnticipo extends Migration
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
        $this->schema->create('atc_anticipos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_anticipo');
            $table->string('codigo');
            $table->dateTime('fecha_anticipo');
            $table->morphs('empezable');
            $table->morphs('depositable');
            $table->decimal('total', 20, 2);
            $table->integer('empresa_id')->unsigned();
            $table->string('estado', 100);
            $table->timestamps();
        });

    }

    public function down()
    {
        $this->schema->dropIfExists('atc_anticipos');
    }
}
