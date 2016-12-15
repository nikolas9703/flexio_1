<?php

use \Flexio\Migration\Migration;

class AddCatalogoSolicitudes3 extends Migration
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
    public function up() {
        $data = [
            ['identificador'=>'metodo_pago','valor'=>'ACH','etiqueta'=>'ACH'],                        
            ['identificador'=>'metodo_pago','valor'=>'cheque','etiqueta'=>'Cheque'],
            ['identificador'=>'metodo_pago','valor'=>'efectivo','etiqueta'=>'Efectivo'],                        
            ['identificador'=>'metodo_pago','valor'=>'tarjeta_credito','etiqueta'=>'Tarjeta de cr&eacute;dito'],                        
            ['identificador'=>'metodo_pago','valor'=>'tarjeta_debito','etiqueta'=>'Tarjeta de d&eacute;bito'],                        
            
        ];
        $this->insert('mod_catalogos', $data);
    }
}
