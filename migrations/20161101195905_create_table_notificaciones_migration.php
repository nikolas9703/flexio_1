<?php

use \Flexio\Migration\Migration;

class CreateTableNotificacionesMigration extends Migration
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
        $table = $this->table('not_notificaciones');
        $table->addColumn('uuid_notificacion', 'binary', array('limit' => 16))
            ->addColumn('modulo', 'integer', array('limit' => 10))
            ->addColumn('transaccion', 'integer', array('limit' => 10))
            ->addColumn('roles', 'text', array('limit' => 500))
            ->addColumn('usuarios', 'text', array('limit' => 500))
            ->addColumn('categoria_items', 'integer', array('limit' => 10, 'default' => 0))
            ->addColumn('operador', 'string', array('limit' => 50, 'default' => null))
            ->addColumn('monto', 'decimal',['scale'=>2,'precision'=>10])
            ->addColumn('sin_transaccion', 'integer', array('limit' => 10, 'default' => 0))
            ->addColumn('tipo_notificacion', 'text', array('limit' => 500))
            ->addColumn('estado', 'string', array('limit' => 50))
            ->addColumn('mensaje', 'text', array('limit' => 500))
            ->addColumn('empresa_id', 'integer', array('limit' => 10))
            ->addColumn('updated_at', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->save();
    }

    public function down()
    {
        $this->dropTable('not_notificaciones');
    }
}
