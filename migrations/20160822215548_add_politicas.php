<?php

use \Flexio\Migration\Migration;

class AddPoliticas extends Migration
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
       $exist = $this->hasTable('ptr_transacciones');
       if(!$exist) {
        $this->schema->create('ptr_transacciones', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('categoria_id');
            $table->integer('politable_id');
            $table->string('politable_type');
            $table->integer('role_id');
            $table->integer('transacciones_de');
            $table->integer('transacciones_a');
            $table->decimal('monto_limite');
            $table->integer('empresa_id');
            $table->integer('estado_id');
            $table->integer('usuario_id');
            $table->timestamps();
        });
      }
    }
}
