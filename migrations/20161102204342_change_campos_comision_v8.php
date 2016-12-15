<?php

use \Flexio\Migration\Migration;

class ChangeCamposComisionV8 extends Migration
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
    public function change()
    {
        $this->execute("DELETE FROM com_comisiones_cat  WHERE id_cat > '20'");


        $rows = [
            [
                'id_cat'       => '30',
                'id_campo'       => '0',
                'valor'     => 'pago_parcial',
                'etiqueta'  => 'Pago parcial',
                'identificador'      => 'estado_final'
             ],
             [
                 'id_cat'       => '31',
                 'id_campo'       => '0',
                 'valor'     => 'pago_completo',
                 'etiqueta'  => 'Pago completo',
                 'identificador'      => 'estado_final'
              ]
        ];

        $this->insert('com_comisiones_cat', $rows);

    }
}
