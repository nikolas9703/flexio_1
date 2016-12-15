<?php

use \Flexio\Migration\Migration;

class FlexioCatalogoAnticipoEstado extends Migration
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
              'valor' => 'Por aplicar',
              'etiqueta' => 'por_aplicar',
              'tipo' => 'estado',
              'orden' => 1,
              'modulo' => 'anticipo',
          ],
          [
            'key'  => 2,
            'valor' => 'Aplicado',
            'etiqueta' => 'aplicado',
            'tipo' => 'estado',
            'orden' => 2,
            'modulo' => 'anticipo',
        ],
        [
          'key'  => 3,
          'valor' => 'Anulado',
          'etiqueta' => 'anulado',
          'tipo' => 'estado',
          'orden' => 3,
          'modulo' => 'anticipo',
       ]
      ];
      $this->insert('flexio_catalogos', $rows);
    }
}
