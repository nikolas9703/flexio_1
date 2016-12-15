<?php

use \Flexio\Migration\Migration;

class CatalogoProyectosSeguros extends Migration
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
            ['identificador'=>'tipo_propuesta','valor'=>'licitacion_publica','etiqueta'=>'Licitaci&oacute;n p&uacute;blica'],
            ['identificador'=>'tipo_propuesta','valor'=>'solicitud_precios','etiqueta'=>'Solicitud de precios'],
            ['identificador'=>'tipo_propuesta','valor'=>'concurso_precios','etiqueta'=>'Concurso de precios'],
            ['identificador'=>'tipo_propuesta','valor'=>'acto_publico','etiqueta'=>'Acto p&uacute;blico'],
            ['identificador'=>'tipo_propuesta','valor'=>'compra_menor','etiqueta'=>'Compra menor'],
            ['identificador'=>'validez_fianza','valor'=>'60','etiqueta'=>'60 d&iacute;as'],
            ['identificador'=>'validez_fianza','valor'=>'90','etiqueta'=>'90 d&iacute;as'],
            ['identificador'=>'validez_fianza','valor'=>'120','etiqueta'=>'120 d&iacute;as'],
            ['identificador'=>'validez_fianza','valor'=>'180','etiqueta'=>'180 d&iacute;as']          
        ];
        $this->insert('mod_catalogos', $data);
        
    }    
}
