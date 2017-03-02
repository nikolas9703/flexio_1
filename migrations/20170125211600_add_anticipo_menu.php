<?php

use \Flexio\Migration\Migration;

class AddAnticipoMenu extends Migration
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
        $menu = '{"link": [{"nombre":"Subcontratos","url":"subcontratos\/listar","orden":1},{"nombre":"Anticipos","url":"anticipos\/listar","orden":2}]}';
        $query = "UPDATE modulos SET menu = '".$menu."' WHERE grupo = 'Precio fijo con proveedores'";
        $count = $this->execute($query);
    }
}
