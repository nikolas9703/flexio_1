<?php

use \Flexio\Migration\Migration;

class CreateTipoDocumentosComprasConfig extends Migration
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
        $this->schema->create('doc_documentos_tipos', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_tipo');
            $table->string('nombre', 100);
            $table->string('descripcion',200);
            $table->integer('empresa_id')->unsigned();
            $table->integer('creado_por')->unsigned();
            $table->string('estado', 50)->default("19");
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
        $this->schema->drop('doc_documentos_tipos');
    }
}
