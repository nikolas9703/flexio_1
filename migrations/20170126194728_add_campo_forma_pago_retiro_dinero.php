<?php

use \Flexio\Migration\Migration;

class AddCampoFormaPagoRetiroDinero extends Migration
{
    public function up()
    {
      //inserto el nuevo campo en la base de datos - mov_movimiento_monetario_campos
      $rows = [
          [   /* campo: forma de pago */
              'id_campo'          => '21',
              'nombre_campo'      => 'tipo_pago_id',
              'etiqueta'          => 'Forma de pago',
              'longitud'          => '0',
              'id_tipo_campo'     => '18',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1","data-rule-required":"true","class":"chosen-select tipo_pago"}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => 'catalogo_metodo_pago',
              'requerido'         => '1',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '2'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '22',
              'nombre_campo'      => 'blank_space',
              'etiqueta'          => '',
              'longitud'          => '0',
              'id_tipo_campo'     => '7',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1"}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '0',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '4'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '23',
              'nombre_campo'      => 'numero_cheque',
              'etiqueta'          => 'N&uacute;mero Cheque',
              'longitud'          => '0',
              'id_tipo_campo'     => '14',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1", "class":"form-control numero_cheque","data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '5'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '24',
              'nombre_campo'      => 'nombre_banco_cheque',
              'etiqueta'          => 'Cuenta de Banco',
              'longitud'          => '0',
              'id_tipo_campo'     => '14',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1", "class":"form-control nombre_banco_cheque","data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '6'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '25',
              'nombre_campo'      => 'numero_tarjeta',
              'etiqueta'          => 'N&uacute;mero de tarjeta',
              'longitud'          => '0',
              'id_tipo_campo'     => '14',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1", "class":"form-control numero_tarjeta","data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '7'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '26',
              'nombre_campo'      => 'numero_recibo',
              'etiqueta'          => 'N&uacute;mero de recibo',
              'longitud'          => '0',
              'id_tipo_campo'     => '14',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1", "class":"form-control numero_recibo","data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '8'
          ],          
          [   /* campo: espacio en blanco */
              'id_campo'          => '27',
              'nombre_campo'      => 'banco_proveedor',
              'etiqueta'          => 'Banco del proveedor',
              'longitud'          => '0',
              'id_tipo_campo'     => '18',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1","class":"chosen-select banco_proveedor", "data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => 'catalogo_banco_proveedor',
              'requerido'         => '0',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '8'
          ],
          [   /* campo: espacio en blanco */
              'id_campo'          => '28',
              'nombre_campo'      => 'numero_cuenta_proveedor',
              'etiqueta'          => 'N&uacute;mero de cuenta del proveedor',
              'longitud'          => '0',
              'id_tipo_campo'     => '14',
              'estado'            => 'activo',
              'atributos'         => '{"data-columns":"1", "class":"form-control ach","data-hide-field":true}',
              'agrupador_campo'   => '',
              'contenedor'        => 'div',
              'tabla_relacional'  => '',
              'requerido'         => '',
              'link_url'          => '',
              'fecha_cracion'     => '2017-01-26 19:24:54',
              'posicion'          => '8'
          ]
      ];

      $this->insert('mov_movimiento_monetario_campos', $rows);

      //actualizar posicion de campos
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '9' WHERE `id_campo`='19'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '10' WHERE `id_campo`='5'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '11' WHERE `id_campo`='6'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '12' WHERE `id_campo`='7'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '13' WHERE `id_campo`='8'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '14' WHERE `id_campo`='9'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '15' WHERE `id_campo`='10'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '16' WHERE `id_campo`='11'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '17' WHERE `id_campo`='12'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '18' WHERE `id_campo`='13'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '19' WHERE `id_campo`='14'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '20' WHERE `id_campo`='15'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '21' WHERE `id_campo`='16'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '13' WHERE `id_campo`='17'");
      $this->execute("UPDATE mov_movimiento_monetario_campos SET `posicion`= '10' WHERE `id_campo`='5'");

      //Agregar campo forma de pago
      //para modulo de retiro de dinero
      $rows2 = [[
        'id_panel'  => '96',
        'id_campo'  => '21',
      ],
      [
        'id_panel' => '96',
        'id_campo' => '22'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '23'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '24'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '25'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '26'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '27'
      ],
      [
        'id_panel' => '96',
        'id_campo' => '28'
      ]      
      ];
      $this->insert('mod_panel_campos', $rows2);
      
      //tipo_pago
      $exist = $this->hasTable('mov_retiro_dinero');
      if($exist) {
        $tabla = $this->table('mov_retiro_dinero');

        //Verificar si el campo ya existe
        $column = $tabla->hasColumn('tipo_pago_id');
        $column2 = $tabla->hasColumn('numero_cheque');
        $column3 = $tabla->hasColumn('nombre_banco_cheque');
        $column4 = $tabla->hasColumn('numero_tarjeta');
        $column5 = $tabla->hasColumn('numero_recibo');
        $column6 = $tabla->hasColumn('banco_proveedor');
        $column7 = $tabla->hasColumn('numero_cuenta_proveedor');
        if (!$column) {
          //Agregar campo
          $tabla->addColumn('tipo_pago_id', 'integer', array('limit' => 10, 'after'=>'cuenta_id'))->update();         
        }
      if(!$column2){
         $tabla->addColumn('numero_cheque', 'string', array('limit' => 20, 'after'=>'tipo_pago_id', 'null' => true))->update();          
      }
      if(!$column3){
        $tabla->addColumn('nombre_banco_cheque', 'string', array('limit' => 30, 'after'=>'numero_cheque', 'null' => true))->update();
      }
      if(!$column4){
        $tabla->addColumn('numero_tarjeta', 'integer', array('limit' => 30, 'after'=>'nombre_banco_cheque', 'null' => true))->update();
      }
      if(!$column5){
        $tabla->addColumn('numero_recibo', 'string', array('limit' => 50, 'after'=>'numero_tarjeta', 'null' => true))->update();
      }
      if(!$column6){
         $tabla->addColumn('banco_proveedor', 'integer', array('limit' => 20, 'after'=>'numero_recibo', 'null' => true))->update();
      }
      if(!$column7){
         $tabla->addColumn('numero_cuenta_proveedor', 'string', array('limit' => 30, 'after'=>'banco_proveedor', 'null'=> true))->update();
      }
      }
    }

    public function down()
    {
      $this->execute('DELETE FROM mov_movimiento_monetario_campos WHERE id_campo = 21');
      $this->execute('DELETE FROM mod_panel_campos WHERE id_campo = 21 and id_panel = 96');

      $exist = $this->hasTable('mov_retiro_dinero');
      if($exist) {
        //Verificar si el campo ya existe
        $tabla = $this->table('mov_retiro_dinero');
        $column = $tabla->hasColumn('tipo_pago_id');
        if ($column) {
            //Eliminar campo
            $tabla->removeColumn('tipo_pago_id')->save();
        }
      }

    }
}
