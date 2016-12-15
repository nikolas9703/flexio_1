<?php

use \Flexio\Migration\Migration;

class AddEstadoNuevoPago extends Migration
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

        // Estados de una razon de ajuste
        $rows = [
            [
                'key'    => 18,
                'valor'     => 'Por aprobar',
                'etiqueta'  => 'por_aprobar',
                'tipo'  => 'etapa3',
                'orden'  => 19
            ],

        ];

        $this->insert('cob_cobro_catalogo', $rows);
    }
}
