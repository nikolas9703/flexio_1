<?php

use \Flexio\Migration\Migration;

class TablaClienteRefactory extends Migration
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
        $tabla = $this->table($this->tableName);
        $credito_usado = $tabla->hasColumn('credito_usado');
        $credito = $tabla->hasColumn('credito');

        if ($credito_usado) {
            $this->table($this->tableName)
                 ->renameColumn('credito_usado', 'credito_favor');
        }

        if ($credito) {
            $this->table($this->tableName)
                 ->renameColumn('credito', 'credito_limite');
        }
    }


}
