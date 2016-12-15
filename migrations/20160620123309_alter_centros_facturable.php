<?php

use \Flexio\Migration\Migration;

class AlterCentrosFacturable extends Migration
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


    function up(){

        $this->dropTable('cli_centros_facturacion');
        $tabla = $this->table('cli_centros_facturacion');
        $tabla->addColumn('nombre', 'string', array('limit' => 100))
              ->addColumn('direccion', 'string', array('limit' => 100))    
              ->addColumn('cliente_id','integer',array('limit' => 10))
              ->addColumn('empresa_id','integer',array('limit' => 10))
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime')
              ->save();
    }
}
