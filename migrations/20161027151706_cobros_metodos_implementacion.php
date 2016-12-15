<?php

use \Flexio\Migration\Migration;

class CobrosMetodosImplementacion extends Migration
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
        $this->execute("UPDATE cob_cobro_metodo_pago SET tipo_pago = 'efectivo' where tipo_pago ='al_contado'");
        $this->execute("UPDATE cob_cobro_metodo_pago SET tipo_pago = 'credito_favor' where tipo_pago ='aplicar_credito'");
    }
}
