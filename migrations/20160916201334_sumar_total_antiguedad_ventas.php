<?php

use \Flexio\Migration\Migration;

class SumarTotalAntiguedadVentas extends Migration
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
        $this->execute("DROP function IF EXISTS `fac_ventas_monto`;
        CREATE DEFINER=`root`@`localhost` FUNCTION `fac_ventas_monto`(factura_id INT, total DECIMAL(20,4)) RETURNS decimal(20,4)
BEGIN
  DECLARE monto DECIMAL(20, 4);
  DECLARE cobrado  DECIMAL(20, 4);

  SELECT IFNULL(SUM(cob_cobro_facturas.monto_pagado),0) INTO cobrado FROM cob_cobro_facturas JOIN cob_cobros ON cob_cobros.id = cob_cobro_facturas.cobro_id where cob_cobro_facturas.factura_id = factura_id AND transaccion = 1 AND estado = 'aplicado';

  SET monto = total - cobrado;
RETURN monto;
END");
    }

    public function down()
    {
        $this->execute("DROP function IF EXISTS `fac_ventas_monto`;");
    }
}
