<?php

use \Flexio\Migration\Migration;

class OdtBitacoraMigration extends Migration
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
    public function change()
    {
        $this->schema->create('odt_historial', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_historial');
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('codigo_cuenta')->nullable();;
            $table->integer('empresa_id')->index()->unsigned();
            $table->integer('odt_id')->index()->unsigned();
            $table->integer('usuario_id');
            $table->text('antes')->nullable();;
            $table->text('despues')->nullable();;
            $table->enum('tipo', ['creado', 'actualizado', 'comentario'])->default("actualizado");
            $table->timestamps();
        });

    }
}
