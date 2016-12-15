<?php

use \Flexio\Migration\Migration;

class OrdenesComprasV1 extends Migration
{
    public function up()
    {
        //inserto el nuevo campo en la base de datos - observaciones
        $rows = [
            [
                'id_campo'          => '30',
                'nombre_campo'      => 'observaciones',
                'etiqueta'          => 'Observaciones',
                'longitud'          => '0',
                'id_tipo_campo'     => '15',
                'estado'            => 'activo',
                'atributos'         => '{"class":"form-control observaciones"}',
                'agrupador_campo'   => '',
                'contenedor'        => 'div',
                'tabla_relacional'  => '',
                'requerido'         => '0',
                'link_url'          => '',
                'fecha_cracion'     => '',
                'posicion'          => '42'
            ]
        ];

        $this->insert('ord_ordenes_campos', $rows);
        
        //inserto la union del formulario de creacion y edicion al modulo de ordenes
        $rows2 = [
            [
                'id_panel'  => '52',
                'id_campo'  => '30',
            ],
            [
                'id_panel'  => '53',
                'id_campo'  => '30',
            ]
        ];

        $this->insert('mod_panel_campos', $rows2);
        
        //agrego la nueva columna de observacoines a la tabla de ordenes de compras
        $this->table('ord_ordenes')
                ->addColumn('observaciones', 'text', array('default' => ''))
                ->save();
        
        //actualizo un valor para permitir 4 digitos despues de la coma(,)
        $this->execute("UPDATE ord_ordenes_campos SET `atributos`='{\"style\":\"width:100px;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_unidad\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,4}\',\'greedy\':false\"}' WHERE `id_campo`='21'");
        
        //actualizo la tabla de lines items
        $this->table('lines_items')
                ->changeColumn('precio_unidad', 'decimal', ['scale' => 4, 'precision' => 10]);
    }
    
    public function down()
    {
        $this->execute('DELETE FROM ord_ordenes_campos WHERE id_campo = 30');
        $this->execute('DELETE FROM mod_panel_campos WHERE id_campo = 30 and id_panel = 52');
        $this->execute('DELETE FROM mod_panel_campos WHERE id_campo = 30 and id_panel = 53');
        
        $this->table('ord_ordenes')
                ->removeColumn('observaciones')
                ->save();
        
        //actualizo un valor para permitir 4 digitos despues de la coma(,)
        $this->execute("UPDATE ord_ordenes_campos SET `atributos`='{\"style\":\"width:100px;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_unidad\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}' WHERE `id_campo`='21'");
        
        $this->table('lines_items')
                ->changeColumn('precio_unidad', 'decimal', ['scale' => 2, 'precision' => 10]);
    }
}
