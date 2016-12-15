<?php

use \Flexio\Migration\Migration;

class ColumnaComentarioFacturaCompra extends Migration
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
        $shema = $this->schema;
        $this->schema->table('faccom_facturas_items', function(Illuminate\Database\Schema\Blueprint $table) use($shema){
            if(!$shema->hasColumn('faccom_facturas_items','comentario')){
               $table->string('comentario')->nullable();
            }
        });
    }
}
