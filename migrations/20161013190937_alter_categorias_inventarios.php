<?php

use \Flexio\Migration\Migration;

class AlterCategoriasInventarios extends Migration
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
        $this->schema->table('inv_categorias', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->integer('depreciacion_meses')->default(0);
            $table->float('porcentaje_depreciacion',10,7)->default(0);
            $table->integer('cuenta_id')->unsigned();
            $table->boolean('depreciar')->default(0);
        });
    }
}
