<?php

use \Flexio\Migration\Migration;

class AddLiquidacionesConfig extends Migration
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
      $tabla = $this->table('liq_configuracion')
      ->addColumn('empresa_id', 'integer', ['limit'=>11])
      ->addColumn('nombre','text',['limit'=>200])
      ->addColumn('creado_por','integer', ['limit'=>11])
      ->addColumn('created_at','datetime')
      ->addColumn('updated_at','datetime')
      ->addColumn('estado','integer', ['limit'=>11, 'default'=>1])
      ->save();
    }
}
