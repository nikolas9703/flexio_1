<?php
use \Flexio\Migration\Migration;
use Illuminate\Database\Schema\Blueprint as Blueprint;
use Illuminate\Database\Schema\Builder as Schema;
class NotaCreditoMigration extends Migration
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
        $this->schema->create('venta_nota_creditos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_nota_credito');
            $table->integer('empresa_id');
            $table->integer('cliente_id');
            $table->integer('creado_por');
            $table->integer('centro_contable_id');
            $table->integer('entrada_manual_id');
            $table->string('estado');
            $table->decimal('total',15,2);
            $table->string('codigo',100);
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
