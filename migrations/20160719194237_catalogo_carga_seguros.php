<?php

use \Flexio\Migration\Migration;

class CatalogoCargaSeguros extends Migration
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
            ['identificador'=>'tipo_obligacion','valor'=>'1_97','etiqueta'=>'Obligaci&oacute;n 1-97'],
            ['identificador'=>'tipo_obligacion','valor'=>'2_97','etiqueta'=>'Obligaci&oacute;n 2-97'],
            ['identificador'=>'tipo_obligacion','valor'=>'3_97','etiqueta'=>'Obligaci&oacute;n 3-97'],
            ['identificador'=>'tipo_obligacion','valor'=>'4_97','etiqueta'=>'Obligaci&oacute;n 4-97'],
            ['identificador'=>'tipo_obligacion','valor'=>'5_97','etiqueta'=>'Obligaci&oacute;n 5-97']            
        ];
        $this->insert('mod_catalogos', $data);
        
    }
}
