<?php

use \Flexio\Migration\Migration;

class AddModulosLiquidaciones extends Migration
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
     public function up() {

         $data = [
             ['identificador'=>'estado_liquidacion','valor'=>'1','etiqueta'=>'Activo'],
             ['identificador'=>'estado_liquidacion','valor'=>'2','etiqueta'=>'Inactivo']
         ];
         $this->insert('mod_catalogos', $data);

     }
}
