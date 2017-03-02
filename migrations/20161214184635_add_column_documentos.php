<?php

use \Flexio\Migration\Migration;

class AddColumnDocumentos extends Migration
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
       $table = $this->table('doc_documentos');
       $column = $table->hasColumn('uuid_documento');
       if (!$column) {
         $table->addColumn('uuid_documento', 'binary', array('limit' => 16))->save();
       }
     }
}
