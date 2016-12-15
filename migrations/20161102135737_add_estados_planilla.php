<?php

use \Flexio\Migration\Migration;

class AddEstadosPlanilla extends Migration
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
               'id_cat'       => '30',
               'id_campo'       => '0',
               'valor'     => 'pago_parcial',
               'etiqueta'  => 'Pago parcial',
               'identificador'      => 'estado'
            ],
            [
                'id_cat'       => '31',
                'id_campo'       => '0',
                'valor'     => 'pago_completo',
                'etiqueta'  => 'Pago completo',
                'identificador'      => 'estado'
             ],
             [
                 'id_cat'       => '32',
                 'id_campo'       => '0',
                 'valor'     => 'por_pagar',
                 'etiqueta'  => 'Por pagar',
                 'identificador'      => 'estado'
              ],
       ];

       $this->insert('pln_planilla_cat', $rows);


   }
}
