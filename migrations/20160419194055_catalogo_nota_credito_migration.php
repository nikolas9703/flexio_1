<?php

use \Flexio\Migration\Migration;

class CatalogoNotaCreditoMigration extends Migration
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
        if (!$this->schema->hasTable('venta_nota_credito_catalogo')) {
            $this->schema->create('venta_nota_credito_catalogo', function(Illuminate\Database\Schema\Blueprint $table) {
                $table->increments('id');
                $table->string('tipo',50);
                $table->string('etiqueta',50);
                $table->string('valor',50);
                $table->string('key',50);
                $table->tinyInteger('orden');
            });
       }

    }

    public function down()
    {
      $this->schema->drop('venta_nota_credito_catalogo');
    }
}
