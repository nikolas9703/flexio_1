<?php

use \Flexio\Migration\Migration;

class AddQrCode extends Migration
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
      $exist = $this->hasTable('sec_verified_by_flexio');
      if(!$exist) {
        $this->schema->create('sec_verified_by_flexio', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('link_hash');
            $table->string('document_hash');
            $table->integer('link_validated');
            $table->integer('document_validated');
            $table->string('last_ip');
            $table->timestamps();
        });
      }
    }

    public function down() {
      $exist = $this->hasTable('sec_verified_by_flexio');
      if($exist) {
  			$this->dropTable('sec_verified_by_flexio');
  		}
  	}
}
