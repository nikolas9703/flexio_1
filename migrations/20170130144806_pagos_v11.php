<?php

use \Flexio\Migration\Migration;

class PagosV11 extends Migration
{
    public function up()
    {
        $this->table('pag_pagos')
        ->changeColumn('formulario', 'enum', ['default' => 'factura', 'values' => ['factura','proveedor','subcontrato','planilla','pago_extraordinario','caja','transferencia','movimiento_monetario','retenido']])
        ->save();
    }

    public function down()
    {
        //... nothing
    }
}
