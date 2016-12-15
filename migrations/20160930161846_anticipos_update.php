<?php

use \Flexio\Migration\Migration;

class AnticiposUpdate extends Migration
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

        //delete anticipos_metodos
        //anticipables

        $this->schema->table('atc_anticipos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->renameColumn('empezable_id', 'anticiplable_id');
            $table->renameColumn('empezable_type', 'anticiplable_type');
            $table->renameColumn('total', 'monto');
            $table->string('metodo_anticipo', 100);
            $table->text('referencia')->nullable();
        });

        $this->schema->dropIfExists('atc_anticipos_metodos');
        $this->schema->dropIfExists('anticipables');
    }
}
