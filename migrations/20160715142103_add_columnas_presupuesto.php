<?php

use \Flexio\Migration\Migration;

class AddColumnasPresupuesto extends Migration
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
        $this->schema->table('pres_presupuesto', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->integer('usuario_id')->unsigned();
        });

        $this->schema->table('pres_presupuesto_cuenta_centro', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->integer('usuario_id')->unsigned();
            $table->decimal('porcentaje',5, 2)->default(0);
        });

    }
}
