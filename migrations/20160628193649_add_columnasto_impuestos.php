<?php

use \Flexio\Migration\Migration;

class AddColumnastoImpuestos extends Migration
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
    public function change() {
        $this->schema->table('contab_impuestos', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->enum('retiene_impuesto',['si', 'no'])->default('no');
            $table->decimal('porcentaje_retenido', 5, 2)->default('0.00');
            $table->integer('cuenta_retenida_id')->unsigned()->default(0);
        });
    }
}
