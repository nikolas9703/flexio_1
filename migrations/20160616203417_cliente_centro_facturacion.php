<?php

use \Flexio\Migration\Migration;

class ClienteCentroFacturacion extends Migration
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
        //$exists = $this->hasTable('cli_centros_facturacion');
        $exists = $this->schema->hasTable('cli_centros_facturacion');
        if(!$exists){
                $this->schema->create('cli_centros_facturacion', function(Illuminate\Database\Schema\Blueprint $table) {
                    $table->integer('centro_id')->unsigned()->index();
                    $table->integer('cliente_id')->unsigned()->index();
                    $table->string('direccion');
                    $table->timestamps();
                });
        }
    }

    public function down()
    {
        //$this->dropTable('cli_centros_facturacion');
    }
}
