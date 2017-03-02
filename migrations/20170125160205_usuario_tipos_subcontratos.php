<?php

use \Flexio\Migration\Migration;

class UsuarioTiposSubcontratos extends Migration
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
      $exist = $this->hasTable('usuarios_tipos_subcontratos');
      if(!$exist) {
        $table = $this->table('usuarios_tipos_subcontratos', array('id' => false, 'primary_key' => array('usuario_id', 'tipo_subcontrato_id','empresa_id')));
        $table->addColumn('usuario_id', 'integer')
              ->addColumn('tipo_subcontrato_id', 'integer')
              ->addColumn('empresa_id','integer')
              ->addIndex(array('usuario_id', 'tipo_subcontrato_id','empresa_id'), array('unique' => true))
              ->create();
      }
    }
}
