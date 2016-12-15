<?php

use \Flexio\Migration\Migration;

class CatalogoConfiguracionComprasMigration extends Migration
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
        $table = $this->table('cat_categoria_proveedor');
        $table
            ->addColumn('uuid_categoria', 'binary', array('limit' => 16))
            ->addColumn('nombre', 'string', array('limit' => 100))
            ->addColumn('descripcion', 'string', array('limit' => 100))
            ->addColumn('estado', 'string', array('limit' => 100, 'default'=> 'activo'))
            ->addColumn('empresa_id', 'integer', array('limit' => 10))
            ->addColumn('created_by', 'integer', array('limit' => 10))
            ->addColumn('updated_at', 'datetime')
            ->addColumn('created_at', 'datetime')

            ->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('cat_categoria_proveedor');
    }
}
