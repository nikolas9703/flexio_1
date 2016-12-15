<?php

use \Flexio\Migration\Migration;

class CatalogoCobrosTipoCobroFlexio extends Migration
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
      $rows = [
          [
            'key'  => 1,
            'valor' => 'Depositar en cuenta de Banco:',
            'etiqueta' => 'banco',
            'tipo' => 'tipo_cobro',
            'orden' => 1,
            'modulo' => 'cobro',
        ],
        [
          'key'  => 2,
          'valor' => 'Recibir en Caja:',
          'etiqueta' => 'caja',
          'tipo' => 'tipo_cobro',
          'orden' => 2,
          'modulo' => 'cobro',
      ]
    ];
      $this->insert('flexio_catalogos', $rows);
    }
}
