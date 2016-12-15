<?php

use \Flexio\Migration\Migration;

class ContratosAlquilerCatalogosAddMigration extends Migration
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
        
        //$this->execute("UPDATE opo_oportunidades_catalogos SET nombre='".  utf8_decode("En negociaciÃ³n")."' WHERE id = '2'");
         // Estados de una razon de ajuste
         $rows = [
             [
                 'nombre'     => 'Activo depreciable',
                 'valor'  => 'Activo depreciable',
                 'tipo'  => 'tipos_cuenta'
             ],
             [
                 'nombre'     => 'Artículos de oficina',
                 'valor'  => 'Artículos de oficina',
                 'tipo'  => 'tipos_cuenta'
             ],
             [
                'nombre'     => '7%',
                'valor'  => '7',
                'tipo'  => 'impuesto'
              ],
             [
              'nombre'     => '10%',
              'valor'  => '10',
              'tipo'  => 'impuesto'
             ],
         ];
         
         $this->insert('conalq_contratos_alquiler_catalogos', $rows);
        
    }
}
