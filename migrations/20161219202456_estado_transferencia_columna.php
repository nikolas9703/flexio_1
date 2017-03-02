<?php

use \Flexio\Migration\Migration;

class EstadoTransferenciaColumna extends Migration
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
         $this->table('ca_transferencias')
         ->addColumn('estado', 'string', ['limit' => 140, 'default' => 'por_aprobar','after'=>'creado_por'])
         ->save();
     }

     public function down()
     {
         $this->table('ca_transferencias')
         ->removeColumn('estado')
         ->save();
     }

}
