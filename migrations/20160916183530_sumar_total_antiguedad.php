<?php

use \Flexio\Migration\Migration;

class SumarTotalAntiguedad extends Migration
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
         $count = $this->execute("DROP function IF EXISTS `fac_compras_monto`;
/*
   Esta function es usada para obtener los monto pendiente de la facturas compras
   para el reporte de antiguedad de proveedores
*/

CREATE FUNCTION `fac_compras_monto` (proveedor_id INT, factura_id INT, empresa_id INT, total DECIMAL(20, 4))
RETURNS DECIMAL(20, 4)
BEGIN
  DECLARE monto DECIMAL(20, 4);
  DECLARE proveedor_retiene varchar(10) default '';
  DECLARE empresa_retiene varchar(10) default '';
  DECLARE pagado  DECIMAL(20, 4);
  DECLARE retenido DECIMAL(20, 4);

  SELECT retiene_impuesto INTO proveedor_retiene FROM pro_proveedores WHERE id = proveedor_id;

  SELECT retiene_impuesto INTO empresa_retiene FROM empresas WHERE id = empresa_id;

  SELECT IFNULL(SUM(pag_pagos_pagables.monto_pagado),0) INTO pagado FROM pag_pagos_pagables JOIN pag_pagos ON pag_pagos.id = pag_pagos_pagables.pago_id where pagable_id = factura_id AND pagable_type = 'Facturas_compras_orm' AND estado = 'aplicado';

  IF (proveedor_retiene = 'no' AND empresa_retiene = 'si' AND total > 500) THEN

    SELECT IFNULL(SUM(faccom_facturas_items.retenido),0) INTO retenido FROM faccom_facturas_items WHERE faccom_facturas_items.factura_id = factura_id;

    SET monto = total - (pagado + retenido);
  ELSE
    SET monto = total - pagado;
  END IF;


RETURN monto;
END"); // returns the number of affected rows
    }

    public function down()
    {
      $this->execute('DROP function IF EXISTS `fac_compras_monto`;');
    }
}
