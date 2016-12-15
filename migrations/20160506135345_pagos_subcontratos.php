<?php

use \Flexio\Migration\Migration;

class PagosSubcontratos extends Migration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pagos = $this->table('pag_pagos');
        $pagos->changeColumn('formulario', 'enum', array('values' => ['factura', 'proveedor', 'subcontrato']))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
