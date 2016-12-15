<?php

use \Flexio\Migration\Migration;

class AddCatalogoSolicitudes extends Migration
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
            ['identificador'=>'pagador_seguros','valor'=>'cliente','etiqueta'=>'Cliente'],
            ['identificador'=>'pagador_seguros','valor'=>'otro','etiqueta'=>'Asegurado'],
            ['identificador'=>'sitio_pago','valor'=>'aseguradora','etiqueta'=>'Aseguradora'],
            ['identificador'=>'sitio_pago','valor'=>'caja','etiqueta'=>'Caja'],
            ['identificador'=>'sitio_pago','valor'=>'mensajeria','etiqueta'=>'Mensajer&iacute;a'],
            ['identificador'=>'centro_facturacion','valor'=>'direccion_laboral','etiqueta'=>'Direcci&oacute;n Laboral'],
            ['identificador'=>'centro_facturacion','valor'=>'direccion_residencial','etiqueta'=>'Direcci&oacute;n Residencial'],
            
        ];
        $this->insert('mod_catalogos', $data);
    }
}
