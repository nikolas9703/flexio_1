<?php

use \Flexio\Migration\Migration;

class AddValueDefaultAnticipo extends Migration
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
    public function addColumCreado()
    {
        $tabla = $this->table('atc_anticipos');
        $column = $tabla->hasColumn('creado_por');
        if (!$column) {
            $tabla->addColumn('creado_por', 'integer', array('limit' => 10, 'after' => 'empresa_id', 'null' => true))
                ->update();
        }
    }
    public function setValueDefaultAnticipo(){
        $this->schema->table('atc_anticipos', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->integer('depositable_id')->unsigned()->default(0)->change();
            $table->string('depositable_type')->nullable()->change();
            $table->string('metodo_anticipo')->nullable()->change();
        });
    }
    public function change()
    {
       $this->addColumCreado();
        $this->setValueDefaultAnticipo();
    }
}
