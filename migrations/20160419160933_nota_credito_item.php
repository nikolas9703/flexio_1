<?php

use \Flexio\Migration\Migration;

class NotaCreditoItem extends Migration
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
        $this->schema->create('venta_nota_credito_items', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('nota_credito_id')->unsigned();
            $table->integer('cuenta_id')->unsigned();
            $table->decimal('credito',15,2);
             $table->string('descripcion',200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->drop('venta_nota_creditos');
    }

}
