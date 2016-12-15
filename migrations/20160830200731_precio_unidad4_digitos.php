<?php

use \Flexio\Migration\Migration;

class PrecioUnidad4Digitos extends Migration
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
      $tabla = $this->table('faccom_facturas_items');
      $column = $tabla->hasColumn('precio_unidad');
      if (!$column) {
        $this->schema->table('faccom_facturas_items', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->decimal('precio_unidad', 20, 4)->change();
        });
      }

      $tabla = $this->table('lines_items');
      $column = $tabla->hasColumn('precio_unidad');
      if (!$column) {
        $this->schema->table('lines_items', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->decimal('precio_unidad', 20, 4)->change();
        });
      }
    }
}
