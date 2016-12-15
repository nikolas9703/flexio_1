<?php

use \Flexio\Migration\Migration;

class ClientesCorreos extends Migration
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
        $this->schema->create('cli_clientes_correos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('cliente_id');
            $table->string('correo',250);
            $table->string('tipo',250);
            $table->timestamps();
        });
    }
}
