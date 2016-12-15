<?php

use \Flexio\Migration\Migration;

class AddColumnsToContratoItems extends Migration
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
        $this->schema->table('contratos_items', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->string('periodo_tarifario',50)->nullable();
            $table->decimal('impuesto_total', 20, 2)->default(0);
            $table->decimal('descuento_total', 20, 2)->default(0);
            $table->decimal('precio_total', 20, 2)->default(0);
            $table->decimal('precio_unidad', 20, 2)->default(0);
            $table->integer('impuesto_id')->unsigned();
        });
    }
}
