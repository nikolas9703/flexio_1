<?php

use \Flexio\Migration\Migration;

class PagosV6 extends Migration
{
  public function up()
    {
        $conn = $this->getAdapter()->getConnection();
        $quotedString = $conn->quote('Flexio\Modulo\FacturasCompras\Models\FacturaCompra');
        $this->execute("UPDATE pag_pagos_pagables SET pagable_type = $quotedString where pagable_type = 'FlexioModuloFacturasComprasModelsFacturaCompra'");
    }

    public function down()
    {
        $conn = $this->getAdapter()->getConnection();
        $quotedString = $conn->quote('Flexio\Modulo\FacturasCompras\Models\FacturaCompra');
        $this->execute("UPDATE pag_pagos_pagables SET pagable_type = 'FlexioModuloFacturasComprasModelsFacturaCompra' where pagable_type = $quotedString");
    }
}
