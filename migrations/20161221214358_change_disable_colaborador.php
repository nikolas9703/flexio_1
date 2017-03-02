<?php

use \Flexio\Migration\Migration;

class ChangeDisableColaborador extends Migration
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
       $this->execute("UPDATE col_colaboradores_campos SET `atributos`='{\"readonly\":\"readonly\"}'  WHERE id_campo = '101'");
     }
}

//$this->execute("UPDATE ord_ordenes_campos SET `atributos`='{\"style\":\"width:100px;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_unidad\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,4}\',\'greedy\':false\"}' WHERE `id_campo`='21'");
