<?php

use \Flexio\Migration\Migration;

class ModCatalogosCondicionArticuloV1 extends Migration
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
        $rows = [
            [
              'identificador'    => 'condicion_articulo',
              'valor'  => 'nuevo',
              'etiqueta'  => 'Nuevo',
              'orden'  => 1,
              'activo'  => 1
            ],
            [
              'identificador'    => 'condicion_articulo',
              'valor'  => 'usado',
              'etiqueta'  => 'Usado',
              'orden'  => 2,
              'activo'  => 1
            ]
        ];

        $this->insert('mod_catalogos', $rows);
        
        
        
        
    }

    public function down()
    {
        $this->execute("DELETE FROM mod_catalogos WHERE identificador = 'condicion_articulo'");
    }

}
