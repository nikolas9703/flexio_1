<?php

use \Flexio\Migration\Migration;

class EditSeguroMaritimo extends Migration
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
        $tabla = $this->table('int_casco_maritimo');
        $tabla->changeColumn('numero', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('serie', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('nombre_embarcacion', 'string', array('limit' => 100, 'null' => true))                                
                ->changeColumn('tipo', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('marca', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('valor', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('pasajeros', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('acreedor', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('porcentaje_acreedor', 'string', array('limit' => 100, 'null' => true))                
                ->changeColumn('observaciones', 'text', array('limit' => 500, 'null' => true))
                ->changeColumn('tipo_id', 'string', array('limit' => 100, 'null' => true))
                ->changeColumn('updated_at', 'datetime')
                ->changeColumn('created_at', 'datetime')
              ->update();
    }
}
