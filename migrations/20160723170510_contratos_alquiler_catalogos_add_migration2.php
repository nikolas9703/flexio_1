<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerCatalogosAddMigration2 extends Migration
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
public function up(){
        
        //$this->execute("UPDATE opo_oportunidades_catalogos SET nombre='".  utf8_decode("En negociación")."' WHERE id = '2'");
         // Estados de una razon de ajuste
         $rows = [
             [
             'nombre'     => 'Exento - Libre de impuestos',
             'valor'  => 'exento',
             'tipo'  => 'impuesto'
                 ],
             [
             'nombre'     => 'I.T.B.M.S. 7%',
             'valor'  => '7',
             'tipo'  => 'impuesto'
                 ],
              [
                'nombre'     => 'Impuestos fiscales',
                'valor'  => 'Impuestos fiscales',
                'tipo'  => 'impuesto'
              ],
             [
              'nombre'     => 'Impuestos municipales',
              'valor'  => 'Impuestos municipales',
              'tipo'  => 'impuesto'
             ],
         ];
         
         $this->insert('conalq_contratos_alquiler_catalogos', $rows);
        
    }
}
