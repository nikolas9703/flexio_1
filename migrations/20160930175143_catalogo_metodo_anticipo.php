<?php

use \Flexio\Migration\Migration;

class CatalogoMetodoAnticipo extends Migration
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
               'key'  => 4,
               'valor' => 'Efectivo',
               'etiqueta' => 'efectivo',
               'tipo' => 'metodo_anticipo',
               'orden' => 1,
               'modulo' => 'anticipo',
           ],
           [
             'key'  => 5,
             'valor' => 'Credito a favor',
             'etiqueta' => 'credito_favor',
             'tipo' => 'metodo_anticipo',
             'orden' => 2,
             'modulo' => 'anticipo',
         ],
         [
           'key'  => 6,
           'valor' => 'Cheque',
           'etiqueta' => 'cheque',
           'tipo' => 'metodo_anticipo',
           'orden' => 3,
           'modulo' => 'anticipo',
         ],
        [
             'key'  => 7,
             'valor' => 'Tarjeta de credito',
             'etiqueta' => 'tarjeta_credito',
             'tipo' => 'metodo_anticipo',
             'orden' => 3,
             'modulo' => 'anticipo',
        ],
        [
            'key'  => 8,
            'valor' => 'ACH',
            'etiqueta' => 'ach',
            'tipo' => 'metodo_anticipo',
            'orden' => 3,
            'modulo' => 'anticipo',
         ],
        [
            'key'  => 9,
            'valor' => 'Caja menuda',
            'etiqueta' => 'caja_menuda',
            'tipo' => 'metodo_anticipo',
            'orden' => 3,
            'modulo' => 'anticipo',
         ]
       ];
       $this->insert('flexio_catalogos', $rows);
     }
}
