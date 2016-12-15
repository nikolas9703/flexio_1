<?php

use \Flexio\Migration\Migration;

class UpdateColumIdentificacionClienteMigration extends Migration
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

    protected $tableName = 'cli_clientes';
    public function up()
    {

       /* $exists = $this->hasTable($this->tableName);
        if ($exists) {
            $this->table($this->tableName)
                ->changeColumn('identificacion', 'string', ['limit'=>140, 'null' => true])
                ->addIndex(array('identificacion'), array('unique' => true))
                ->save();
        }*/
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
