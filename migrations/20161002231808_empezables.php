<?php

use \Flexio\Migration\Migration;

class Empezables extends Migration
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
    public function up()
    {
        $this->schema->create('empezables', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->integer('anticipo_id')->unsigned();
            $table->morphs('empezable');
        });

        $this->schema->table('atc_anticipos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->renameColumn('anticiplable_id', 'anticipable_id');
            $table->renameColumn('anticiplable_type', 'anticipable_type');
        });
    }
}
