<?php

use \Flexio\Migration\Migration;

class OrdenesVentasV1 extends Migration
{
    public function up()
    {
        $this->execute("UPDATE ord_orden_venta_catalogo SET valor='Por aprobar' WHERE id = '7'");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("UPDATE ord_orden_venta_catalogo SET valor='Abierta' WHERE id = '7'");
    }
}
