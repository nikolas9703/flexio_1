<?php

use \Flexio\Migration\Migration;

class AddRowsExtraordinario2 extends Migration
{

      public function up()
    {
        $rows = [
            [
                'id_campo'       => '0',
                'valor'     => 'pago_parcial',
                'etiqueta'  => 'Pago parcial',
                'identificador'      => 'estado_final'
             ],
             [
                 'id_campo'       => '0',
                 'valor'     => 'pago_completo',
                 'etiqueta'  => 'Pago completo',
                 'identificador'      => 'estado_final'
              ]
        ];

        $this->insert('com_comisiones_cat', $rows);


    }
}
