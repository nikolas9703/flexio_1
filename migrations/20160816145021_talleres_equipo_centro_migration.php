<?php

use \Flexio\Migration\Migration;

class TalleresEquipoCentroMigration extends Migration
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
    public function up() {

        $this->schema->hasTable('tal_centros', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('equipo_id');
            $table->integer('centro_padre_id');
            $table->integer('centro_id');
            $table->integer('departamento_id');
            $table->timestamps();
        });
    }
}
