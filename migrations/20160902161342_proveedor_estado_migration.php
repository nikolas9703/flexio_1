<?php

use \Flexio\Migration\Migration;

class ProveedorEstadoMigration extends Migration
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
    /**
     * Migrate Up.
     */
    protected $tableName = 'pro_proveedores';
    public function up()
    {

        $exists = $this->hasTable($this->tableName);
        if ($exists) {

            $this->table($this->tableName)
                ->changeColumn('estado', 'string', ['limit'=>140,'default'=>'por_aprobar'])
                ->save();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
