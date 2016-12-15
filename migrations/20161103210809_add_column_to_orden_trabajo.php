<?php

use \Flexio\Migration\Migration;

class AddColumnToOrdenTrabajo extends Migration
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
    public function change()
    {
        if (!$this->schema->hasColumn('odt_ordenes_trabajo','equipo_trabajo_id')) {
            $this->schema->table('odt_ordenes_trabajo', function(Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('equipo_trabajo_id')->unsigned()->nullable()->default(0);
             });
        }

        if (!$this->schema->hasColumn('odt_ordenes_trabajo','centro_facturable_id')) {
            $this->schema->table('odt_ordenes_trabajo', function(Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('centro_facturable_id')->unsigned()->nullable()->default(0);
             });
        }
    }
}
