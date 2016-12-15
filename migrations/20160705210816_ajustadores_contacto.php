<?php

use \Flexio\Migration\Migration;

class AjustadoresContacto extends Migration
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
    public function up() {
        $this->_table_ajustadores_contacto();         
    }
    public function down() {
        $this->dropTable('int_persona');        
    }
    
    private function _table_ajustadores_contacto() {
        $table = $this->table('seg_ajustadores_contacto');
        $table->addColumn('nombre', 'string', array('limit' => 100))
                ->addColumn('apellido', 'string', array('limit' => 100))
                ->addColumn('cargo', 'string', array('limit' => 100))                
                ->addColumn('telefono', 'string', array('limit' => 100))
                ->addColumn('ajustador_id', 'integer', array('limit' => 10))
                ->addColumn('celular', 'string', array('limit' => 100))
                ->addColumn('email', 'string', array('limit' => 100))
                ->addColumn('updated_at', 'datetime')
                ->addColumn('created_at', 'datetime')                         
                ->save();
    }
}
