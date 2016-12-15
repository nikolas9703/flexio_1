<?php

use \Flexio\Migration\Migration;

class AddTableOrdenHistorical extends Migration
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
        $this->schema->create('ord_ordenes_historial', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_historial');
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('codigo_cuenta');
            $table->integer('empresa_id');
            $table->integer('orden_id');
            $table->integer('usuario_id');
            $table->text('antes');
            $table->text('despues');
            $table->enum('tipo', ['creado', 'actualizado', 'comentario'])->default("actualizado");
            
             
                     
             $table->timestamps();
        });
    }
}
