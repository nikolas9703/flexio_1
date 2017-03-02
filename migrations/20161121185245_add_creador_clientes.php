<?php

use \Flexio\Migration\Migration;

class AddCreadorClientes extends Migration
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
      if (!$this->schema->hasColumn('cli_clientes','creado_por')) {
          $this->schema->table('cli_clientes', function(Illuminate\Database\Schema\Blueprint $table) {
              $table->integer('creado_por')->unsigned()->nullable()->default(0);
           });
      }
    }
}
