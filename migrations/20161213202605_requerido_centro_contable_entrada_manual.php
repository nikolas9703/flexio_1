<?php

use \Flexio\Migration\Migration;

class RequeridoCentroContableEntradaManual extends Migration
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
         $this->execute("UPDATE contab_entrada_manual_campos SET atributos='{\"class\":\"chosen-select form-control\", \"data-rule-required\":\"true\"}', requerido = 1  WHERE id_campo = 7 AND nombre_campo = 'centro_id'");
    }
}
