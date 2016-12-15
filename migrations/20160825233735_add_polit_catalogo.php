<?php

use \Flexio\Migration\Migration;

class AddPolitCatalogo extends Migration
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
      $exist = $this->hasTable('ptr_transacciones_catalogo');
      if(!$exist) {
        $this->schema->create('ptr_transacciones_catalogo', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('valor');
            $table->string('etiqueta');
            $table->string('tipo');
            $table->integer('orden');
        });
      }
    }

    public function down() {
      $exist = $this->hasTable('ptr_transacciones_catalogo');
      if($exist) {
  			$this->dropTable('ptr_transacciones_catalogo');
  		}
  	}
}
