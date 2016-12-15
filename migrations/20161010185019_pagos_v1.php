<?php

use \Flexio\Migration\Migration;

class PagosV1 extends Migration
{
    public function up()
    {
        $this->execute('DELETE FROM pag_pagos_catalogo');
    }

    public function down()
    {
        //sin rollback
    }
}
