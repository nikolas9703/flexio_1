<?php

use \Flexio\Migration\Migration;

class PagosV9 extends Migration
{
  public function up()
    {
        $this->table('pag_pagos_metodo_pago')
        ->changeColumn('total_pagado','decimal',['scale' => 4, 'precision' => 10, 'default' => 0])
        ->save();
    }

    public function down()
    {
        $this->table('pag_pagos_metodo_pago')
        ->changeColumn('total_pagado','decimal',['scale' => 2, 'precision' => 10, 'default' => 0])
        ->save();
    }
}
