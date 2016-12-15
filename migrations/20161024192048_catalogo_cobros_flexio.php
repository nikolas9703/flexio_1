<?php

use \Flexio\Migration\Migration;

class CatalogoCobrosFlexio extends Migration
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
            'valor' => 'Efectivo',
            'etiqueta' => 'efectivo',
            'tipo' => 'metodo_cobro',
            'orden' => 1,
            'modulo' => 'cobro',
        ],
        [
          'key'  => 2,
          'valor' => 'Credito a favor',
          'etiqueta' => 'credito_favor',
          'tipo' => 'metodo_cobro',
          'orden' => 2,
          'modulo' => 'cobro',
      ],
      [
        'key'  => 3,
        'valor' => 'Cheque',
        'etiqueta' => 'cheque',
        'tipo' => 'metodo_cobro',
        'orden' => 3,
        'modulo' => 'cobro',
      ],
     [
          'key'  => 4,
          'valor' => 'Tarjeta de credito',
          'etiqueta' => 'tarjeta_credito',
          'tipo' => 'metodo_cobro',
          'orden' => 4,
          'modulo' => 'cobro',
     ],
     [
         'key'  => 5,
         'valor' => 'ACH',
         'etiqueta' => 'ach',
         'tipo' => 'metodo_cobro',
         'orden' => 5,
         'modulo' => 'cobro',
      ],
      [
          'key'  => 1,
          'valor' => 'Aplicado',
          'etiqueta' => 'aplicado',
          'tipo' => 'estado',
          'orden' => 1,
          'modulo' => 'cobro',
       ],
       [
           'key'  => 2,
           'valor' => 'Anulado',
           'etiqueta' => 'anulado',
           'tipo' => 'estado',
           'orden' => 2,
           'modulo' => 'cobro',
        ]
    ];
    $this->insert('flexio_catalogos', $rows);
  }

}
