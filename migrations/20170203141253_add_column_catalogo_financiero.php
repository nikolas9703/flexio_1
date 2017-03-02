<?php

use \Flexio\Migration\Migration;

class AddColumnCatalogoFinanciero extends Migration
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
       
    $rows = [[
        'tipo' => 'reporte',
        'etiqueta' => 'impuestos_sobre_itbms',
        'valor' => 'Reporte de retenci&oacute;n de I.T.B.M.S. por proveedor',
        'orden' => 14
      ]    
      ];
      $exist = $this->hasTable('cat_reporte_financiero');
      if($exist) {
      $this->insert('cat_reporte_financiero', $rows); 
      }
    }
}
