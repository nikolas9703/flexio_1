<?php

use \Flexio\Migration\Migration;

class CobrosRefactoryImplementacion extends Migration
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
        $this->execute("UPDATE cob_cobros SET empezable_type = 'Flexio\\\Modulo\\\FacturasVentas\\\Models\\\FacturaVenta', empezable_id=(select cobrable_id from cob_cobro_facturas where cobro_id = cob_cobros.id limit 1) where formulario ='factura'");

        $this->execute("UPDATE cob_cobros SET empezable_type = 'Flexio\\\Modulo\\\Cliente\\\Models\\\Cliente', empezable_id= cliente_id where formulario ='cliente'");
    }
}
