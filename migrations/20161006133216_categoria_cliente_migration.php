<?php

use \Flexio\Migration\Migration;

class CategoriaClienteMigration extends Migration
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
        $this->schema->create('cli_clientes_categoria', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_categoria');
            $table->string('nombre');
            $table->string('descripcion');
            $table->string('estado')->default('activo');
            $table->integer('id_empresa');
            $table->integer('creado_por');
            $table->timestamps();
        });
    }

    public function down(){
        $this->schema->dropIfExists('cli_clientes_categoria');
    }
}
