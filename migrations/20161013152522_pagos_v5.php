<?php

use \Flexio\Migration\Migration;

class PagosV5 extends Migration
{
    public function up()
    {
        $this->execute("UPDATE pag_pagos_pagables SET pagable_type = 'Flexio\\Modulo\\FacturasCompras\\Models\\FacturaCompra' where pagable_type = 'Facturas_compras_orm'");
    }

    public function down()
    {
        $this->execute("UPDATE pag_pagos_pagables SET pagable_type = 'Facturas_compras_orm' where pagable_type = 'Flexio\\Modulo\\FacturasCompras\\Models\\FacturaCompra'");
    }
}
