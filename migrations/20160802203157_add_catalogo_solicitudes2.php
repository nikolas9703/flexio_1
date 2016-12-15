<?php

use \Flexio\Migration\Migration;

class AddCatalogoSolicitudes2 extends Migration
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
            ['identificador'=>'cantidad_pagos','valor'=>'1','etiqueta'=>'1'],
            ['identificador'=>'cantidad_pagos','valor'=>'2','etiqueta'=>'2'],
            ['identificador'=>'cantidad_pagos','valor'=>'4','etiqueta'=>'4'],
            ['identificador'=>'cantidad_pagos','valor'=>'6','etiqueta'=>'6'],
            ['identificador'=>'cantidad_pagos','valor'=>'12','etiqueta'=>'12'],
            ['identificador'=>'frecuencia_pagos','valor'=>'anual','etiqueta'=>'Anual'],
            ['identificador'=>'frecuencia_pagos','valor'=>'semestral','etiqueta'=>'Semestral'],
            ['identificador'=>'frecuencia_pagos','valor'=>'trimestral','etiqueta'=>'Trimestral'],
            ['identificador'=>'frecuencia_pagos','valor'=>'mensual','etiqueta'=>'Mensual'],            
            
        ];
        $this->insert('mod_catalogos', $data);
    }
}
